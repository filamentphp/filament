for (const [file, schema] of Object.entries(require('schema'))) {
    console.log(`${file}: ${schema}`)
}
