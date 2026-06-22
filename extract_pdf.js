const fs = require("fs");
const buf = fs.readFileSync("memoria_antigua.pdf");
const str = buf.toString("binary");

const lines = [];
let current = "";
for (let i = 0; i < str.length; i++) {
  const c = str.charCodeAt(i);
  if (c >= 32 && c < 127) {
    current += str[i];
  } else {
    if (current.length > 5) lines.push(current);
    current = "";
  }
}
if (current.length > 5) lines.push(current);

const text = lines.filter(l => /[a-zA-Z]{3,}/.test(l) && !/^[/<>]/.test(l.trim()));
process.stdout.write(text.join("\n").slice(0, 60000));
