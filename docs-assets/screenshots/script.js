for (const [file, schema] of Object.entries(require('schema.json'))) {
    console.log(`${file}: ${schema}`);
}
