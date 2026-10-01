export default function passwordRevealFormComponent() {
    return {
        isPasswordRevealed: false,

        passwordRevealFocusRequest: 0,

        destroy() {
            this.passwordRevealFocusRequest++
        },

        setPasswordRevealed(isPasswordRevealed) {
            const passwordInput = this.$root.querySelector('input.fi-input')
            const passwordRevealFocusRequest = ++this.passwordRevealFocusRequest
            const selectionStart = passwordInput.selectionStart
            const selectionEnd = passwordInput.selectionEnd
            const selectionDirection = passwordInput.selectionDirection

            this.isPasswordRevealed = isPasswordRevealed

            this.$nextTick(() => {
                if (
                    passwordRevealFocusRequest !==
                    this.passwordRevealFocusRequest
                ) {
                    return
                }

                requestAnimationFrame(() => {
                    if (
                        passwordRevealFocusRequest !==
                        this.passwordRevealFocusRequest
                    ) {
                        return
                    }

                    const passwordActionName = isPasswordRevealed
                        ? 'hidePasswordAction'
                        : 'showPasswordAction'
                    const passwordAction = Array.from(
                        this.$root.querySelectorAll(
                            `[x-ref="${passwordActionName}"]`,
                        ),
                    ).find(
                        (passwordAction) =>
                            passwordAction.isConnected &&
                            passwordAction.getClientRects().length,
                    )

                    if (!passwordAction) {
                        return
                    }

                    passwordAction.focus()
                    passwordInput.setSelectionRange(
                        selectionStart,
                        selectionEnd,
                        selectionDirection,
                    )
                })
            })
        },
    }
}
