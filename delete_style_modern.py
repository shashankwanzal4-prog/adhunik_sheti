import os

file_path = r"C:\xampp\htdocs\Adhunik_Sheti\assets\css\style-modern.css"

try:
    if os.path.exists(file_path):
        os.remove(file_path)
        print(f"✓ Successfully deleted {file_path}")
    else:
        print(f"File does not exist: {file_path}")
    
    # List remaining CSS files
    css_dir = r"C:\xampp\htdocs\Adhunik_Sheti\assets\css"
    css_files = [f for f in os.listdir(css_dir) if f.endswith('.css')]
    print(f"\nRemaining CSS files:")
    for f in css_files:
        file_size = os.path.getsize(os.path.join(css_dir, f))
        print(f"  - {f}: {file_size} bytes ({file_size/1024:.2f} KB)")
    
except Exception as e:
    print(f"✗ Error: {e}")
