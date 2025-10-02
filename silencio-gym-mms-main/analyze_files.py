#!/usr/bin/env python3
"""
File analysis script to categorize test/debug files
"""

import glob
import os

# File categories
categories = {
    'debug_files': [
        'debug_*', 'investigate_*', 'check_*'
    ],
    'test_files': [
        'test_*', 'verify_*', 'complete_*'
    ],
    'fix_files': [
        'fix_*', 'update_*', 'consolidate_*', 'final_*'
    ],
    'setup_files': [
        'register_*', 'set_*', 'setup_*', 'activate_*'
    ],
    'simulation_files': [
        'simulate_*', 'start_*'
    ],
    'status_files': [
        'check_current_status.php'
    ]
}

all_files = []
for pattern in glob.glob('*.php'):
    all_files.append(pattern)
for pattern in glob.glob('*.py'):
    all_files.append(pattern)

print("FILE CATEGORIZATION:")
print("=" * 50)

for category, patterns in categories.items():
    matches = []
    suffix = f"_{category.split('_')[0]}"
    if suffix.endswith('files'):
        suffix = suffix.replace('files', '')
    
    print(f"\n{category.upper().replace('_', ' ')}:")
    print("-" * 30)
    
    category_files = []
    for pattern in patterns:
        category_files.extend([f for f in all_files if f.startswith(pattern.replace('*', '').split('_')[0])])
    
    category_files = sorted(list(set(category_files)))
    for i, file in enumerate(category_files, 1):
        print(f"{i:2d}. {file}")

print(f"\nSUMMARY:")
print(f"Total files found: {len(all_files)}")
for category, patterns in categories.items():
    count = len([f for f in all_files if any(f.startswith(pattern.replace('*', '').split('_')[0]) for pattern in patterns)])
    print(f"{category}: {count} files")
