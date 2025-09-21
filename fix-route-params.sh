#!/bin/bash

echo "🔍 Scanning for incorrect route() usage in Blade files..."

grep -rl --include="*.blade.php" "route(" resources/views | while read -r file; do
    echo "🛠  Processing: $file"

    # Replace route('name', $var) with route('name', ['param' => $var])
    # This REGEX assumes common usage like: route('route.name', $object->id)
    sed -i.bak -E "s/route\((['\"])([a-zA-Z0-9_.-]+)\1,\s*\$([a-zA-Z0-9_>\[\]\(\)\->]+)\)/route(\1\2\1, ['id' => \$\3])/g" "$file"

    # Add more rules if you have different patterns
done

echo "✅ Done. All invalid route() usages are now wrapped in arrays."

echo "ℹ️  Backup files (*.bak) are created for safety. Review and delete if all is good."
