import schema from 'schema'

for (const [file, screenshot] of Object.entries(schema)) {
    console.log(`${file}: ${screenshot}`);
}
