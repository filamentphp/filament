for (const [file, screenshot] of Object.entries(require('schema.json'))) {
    console.log(`${file}: ${screenshot}`);
}
