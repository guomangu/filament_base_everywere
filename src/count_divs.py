import re

with open('resources/views/livewire/user/profile.blade.php', 'r') as f:
    content = f.read()

# Strip HTML comments
content = re.sub(r'<!--.*?-->', '', content, flags=re.DOTALL)
# Strip blade comments
content = re.sub(r'\{\{--.*?--\}\}', '', content, flags=re.DOTALL)

lines = content.split('\n')
depth = 0
for i, line in enumerate(lines):
    # simple lowercasing and counting
    lower_line = line.lower()
    opens = lower_line.count('<div')
    closes = lower_line.count('</div')
    depth += opens - closes
    
    if depth < 0:
        print(f"NEGATIVE DEPTH at line {i+1}: {line}")
        depth = 0 # reset to find next
        
print(f"Final depth: {depth}")
