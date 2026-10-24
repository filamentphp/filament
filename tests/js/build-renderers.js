import { buildRendererStarters } from './build-renderer-starters.js'

await buildRendererStarters({
    component: {
        packageName: 'schemas',
        stubName: 'SchemaComponent',
        componentStubName: 'SchemaComponent',
    },
})
