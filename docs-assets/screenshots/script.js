const schema = require('./schema.js')

for (const [file, screenshot] of Object.entries(schema)) {
    console.log(`${file}: ${screenshot}`)
}
