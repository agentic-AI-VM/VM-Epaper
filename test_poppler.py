from pdf2image import convert_from_path
import os

PDF_PATH = r"C:\xampp74\htdocs\VidyarthiMitra_Website\epapers\test\input\test.pdf"
OUTPUT_DIR = r"C:\xampp74\htdocs\VidyarthiMitra_Website\epapers\test\output"
POPPLER_PATH = r"C:\poppler-25.12.0\Library\bin"

os.makedirs(OUTPUT_DIR, exist_ok=True)

print("Converting PDF to images...")

pages = convert_from_path(
    PDF_PATH,
    dpi=200,
    poppler_path=POPPLER_PATH
)

for i, page in enumerate(pages):
    out = os.path.join(OUTPUT_DIR, f"page_{i+1}.png")
    page.save(out, "PNG")
    print("Saved:", out)

print("Done.")
