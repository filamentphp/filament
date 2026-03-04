/**
 * Regression test for the activeUploads counter fix.
 * https://github.com/filamentphp/filament/issues/13306
 *
 * Tests the counter logic that prevents shouldUpdateState from being set to
 * true while parallel uploads are still in flight. Run with: node --test tests/js/
 *
 * BEFORE the fix, shouldUpdateState was a simple boolean toggle:
 *   File A starts -> shouldUpdateState = false
 *   File B starts -> shouldUpdateState = false (no-op)
 *   File A done   -> shouldUpdateState = true  <-- VULNERABLE
 *   [poll re-render fires here, state overwrites, File B lost]
 *
 * AFTER the fix, an activeUploads counter gates the flag:
 *   File A starts -> activeUploads=1, shouldUpdateState = false
 *   File B starts -> activeUploads=2, shouldUpdateState = false
 *   File A done   -> activeUploads=1, shouldUpdateState stays false
 *   [poll re-render fires here, state watcher skips -- safe]
 *   File B done   -> activeUploads=0, shouldUpdateState = true
 */

import { describe, it } from 'node:test'
import assert from 'node:assert/strict'

/**
 * Minimal simulation of the file-upload component's upload tracking logic.
 * Extracted from packages/forms/resources/js/components/file-upload.js
 */
function createUploadTracker() {
    return {
        activeUploads: 0,
        shouldUpdateState: true,

        startUpload() {
            this.activeUploads++
            this.shouldUpdateState = false
        },

        finishUpload() {
            this.activeUploads--
            if (this.activeUploads === 0) {
                this.shouldUpdateState = true
            }
        },

        errorUpload() {
            this.activeUploads--
            if (this.activeUploads === 0) {
                this.shouldUpdateState = true
            }
        },

        abortUpload() {
            this.activeUploads--
            if (this.activeUploads === 0) {
                this.shouldUpdateState = true
            }
        },
    }
}

describe('activeUploads counter', () => {
    it('should block state updates while any upload is in flight', () => {
        const tracker = createUploadTracker()

        // Two parallel uploads start
        tracker.startUpload() // File A
        tracker.startUpload() // File B

        assert.equal(tracker.activeUploads, 2)
        assert.equal(tracker.shouldUpdateState, false)

        // File A completes -- state should still be locked
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 1)
        assert.equal(
            tracker.shouldUpdateState,
            false,
            'shouldUpdateState must remain false while File B is still uploading',
        )

        // File B completes -- now state can update
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 0)
        assert.equal(tracker.shouldUpdateState, true)
    })

    it('should handle error during parallel upload without unblocking prematurely', () => {
        const tracker = createUploadTracker()

        tracker.startUpload() // File A
        tracker.startUpload() // File B
        tracker.startUpload() // File C

        // File B errors out
        tracker.errorUpload()
        assert.equal(tracker.activeUploads, 2)
        assert.equal(
            tracker.shouldUpdateState,
            false,
            'shouldUpdateState must remain false -- Files A and C still in flight',
        )

        // File A completes
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 1)
        assert.equal(tracker.shouldUpdateState, false)

        // File C completes
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 0)
        assert.equal(tracker.shouldUpdateState, true)
    })

    it('should handle abort during parallel upload without unblocking prematurely', () => {
        const tracker = createUploadTracker()

        tracker.startUpload() // File A
        tracker.startUpload() // File B

        // File A is aborted by user
        tracker.abortUpload()
        assert.equal(tracker.activeUploads, 1)
        assert.equal(
            tracker.shouldUpdateState,
            false,
            'shouldUpdateState must remain false -- File B still in flight',
        )

        // File B completes
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 0)
        assert.equal(tracker.shouldUpdateState, true)
    })

    it('should allow state updates for single file upload (no regression)', () => {
        const tracker = createUploadTracker()

        tracker.startUpload()
        assert.equal(tracker.shouldUpdateState, false)

        tracker.finishUpload()
        assert.equal(tracker.shouldUpdateState, true)
    })

    it('should simulate the exact race condition scenario from issue #13306', () => {
        const tracker = createUploadTracker()

        // User drops 3 files, maxParallelUploads = 2
        tracker.startUpload() // File A starts
        tracker.startUpload() // File B starts

        // File A completes
        tracker.finishUpload()

        // THIS is the race condition window: a Livewire poll fires here.
        // BEFORE the fix: shouldUpdateState would be true, and the poll
        // would overwrite FilePond state, dropping File B.
        // AFTER the fix: shouldUpdateState is still false because
        // activeUploads > 0.
        assert.equal(
            tracker.shouldUpdateState,
            false,
            'Poll fires after File A completes but File B is still uploading -- state must NOT update',
        )

        // File C starts (was queued behind maxParallelUploads)
        tracker.startUpload()
        assert.equal(tracker.activeUploads, 2)

        // File B completes
        tracker.finishUpload()
        assert.equal(tracker.shouldUpdateState, false)

        // File C completes -- all done
        tracker.finishUpload()
        assert.equal(tracker.activeUploads, 0)
        assert.equal(tracker.shouldUpdateState, true)
    })
})
