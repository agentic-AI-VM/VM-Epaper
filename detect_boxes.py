import os
import cv2
import pytesseract
import numpy as np

# ================= CONFIG =================
TEMP_IMG_DIR = "test/output"
OUTPUT_DIR = "detected_boxes"

os.makedirs(OUTPUT_DIR, exist_ok=True)

MIN_W, MIN_H = 300, 150

EDU_KEYWORDS = [
    "tender", "notice", "notification", "advertisement",
    "admission", "exam", "university", "college",
    "recruitment", "vacancy", "apply",
    "result", "scholarship", "degree",
    "ugc", "cbse", "neet", "jee", "gate",
    "rs.", "date", "no.", "ref", "contact"
]
# ==========================================


def detect_boxes(img):
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    _, thr = cv2.threshold(
        gray, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU
    )

    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (5, 5))
    dil = cv2.dilate(thr, kernel, iterations=2)

    cnts, _ = cv2.findContours(
        dil, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE
    )

    boxes = []
    for c in cnts:
        x, y, w, h = cv2.boundingRect(c)
        aspect = w / float(h)

        if w > MIN_W and h > MIN_H and 0.3 < aspect < 4.0:
            boxes.append((x, y, w, h))

    return boxes


def has_strong_border(crop):
    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
    edges = cv2.Canny(gray, 50, 150)

    h, w = edges.shape
    border_strength = (
        np.sum(edges[0:5, :]) +
        np.sum(edges[-5:, :]) +
        np.sum(edges[:, 0:5]) +
        np.sum(edges[:, -5:])
    )

    return border_strength > 7000


def text_density_ok(crop):
    text = pytesseract.image_to_string(
        crop, config="--psm 6"
    )
    return len(text) > 350


def keyword_match(crop):
    text = pytesseract.image_to_string(
        crop, config="--psm 6"
    ).lower()
    return any(k in text for k in EDU_KEYWORDS)


def looks_like_photo(crop):
    hsv = cv2.cvtColor(crop, cv2.COLOR_BGR2HSV)
    return np.std(hsv[:, :, 1]) > 30


def process_page(img_path):
    img = cv2.imread(img_path)
    if img is None:
        return

    base = os.path.splitext(os.path.basename(img_path))[0]
    boxes = detect_boxes(img)

    count = 0
    for x, y, w, h in boxes:
        crop = img[y:y+h, x:x+w]

        if looks_like_photo(crop):
            continue

        if not has_strong_border(crop):
            continue

        if not keyword_match(crop):
            continue

        if not text_density_ok(crop):
            continue

        out_path = f"{OUTPUT_DIR}/{base}_notice_{count}.png"
        cv2.imwrite(out_path, crop)
        count += 1


def main():
    print("Starting notification extraction...\n")

    for file in os.listdir(TEMP_IMG_DIR):
        if file.lower().endswith(".png"):
            print("Processing:", file)
            process_page(os.path.join(TEMP_IMG_DIR, file))

    print("\nDone.")


if __name__ == "__main__":
    main()
