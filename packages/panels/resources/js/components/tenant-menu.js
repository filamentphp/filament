import normalizeSearchString from '../../../../support/resources/js/utilities/normalize-search-string.js'

export default function tenantMenu() {
    return {
        search: '',

        matchesSearch(name) {
            return (
                this.search === '' ||
                normalizeSearchString(name.replace(/ /g, '')).includes(
                    normalizeSearchString(this.search.replace(/ /g, '')),
                )
            )
        },
    }
}
