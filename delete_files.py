import os

# File paths
file1 = r'C:\xampp\htdocs\Adhunik_Sheti\assets\css\style-modern.css'
file2 = r'C:\xampp\htdocs\Adhunik_Sheti\assets\css\delete_style_modern.bat'

print('=== File Deletion Process ===')
print()

# Delete file1
print(f'Deleting: {file1}')
if os.path.exists(file1):
    os.remove(file1)
    print('✓ File deleted successfully')
    # Verify deletion
    if not os.path.exists(file1):
        print('✓ Verified: File no longer exists')
    else:
        print('✗ Error: File still exists after deletion')
else:
    print('✗ File does not exist (nothing to delete)')

print()

# Delete file2 if it exists
print(f'Checking: {file2}')
if os.path.exists(file2):
    os.remove(file2)
    print('✓ File deleted successfully')
    # Verify deletion
    if not os.path.exists(file2):
        print('✓ Verified: File no longer exists')
    else:
        print('✗ Error: File still exists after deletion')
else:
    print('✓ File does not exist (skipped)')

print()
print('=== Process Complete ===')
