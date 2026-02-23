const fs = require('fs');
let content = fs.readFileSync('resources/views/livewire/user/profile.blade.php', 'utf8');

// Strip html comments
content = content.replace(/<!--[\s\S]*?-->/g, '');
// Strip blade comments
content = content.replace(/{{--[\s\S]*?--}}/g, '');

const lines = content.split('\n');
let depth = 0;
for(let i=0; i<lines.length; i++) {
    const opens = (lines[i].match(/<div(\s|>)/gi) || []).length;
    const closes = (lines[i].match(/<\/div>/gi) || []).length;
    depth += opens - closes;
    if (opens !== closes) {
        // console.log(`${i+1}: ${depth} (+${opens} -${closes})`);
    }
    if (depth < 0) {
        console.log(`NEGATIVE DEPTH at line ${i+1}: ${lines[i]}`);
    }
}
console.log(`Final depth: ${depth}`);
