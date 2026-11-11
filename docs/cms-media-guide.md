# Renzo CMS Media Guide (Images & Videos)

Use this reference whenever adding or updating photos and videos in the Renzo Admin CMS (`/admin`).

---

## 1. Recommended Image Sizes by Website Placement

| Website Section | CMS Location | Display Container (CSS) | Recommended Upload Size | Minimum Size | Aspect Ratio | Auto-Optimized Max Output | Target Upload Size |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Home Hero Sliders** | Admin → Home Sliders | Full width × `490px` min-height (`object-fit: cover`, 34% image opacity over dark gradient) | `1920 × 800 px` (or `1920 × 1080 px`) | `1440 × 600 px` | `16:7` to `16:9` (Landscape) | `1920 × 1080 px` `.webp` | `< 350 KB` |
| **Menu Dish Photos** | Admin → Menu Items | `84 × 84 px` rounded thumbnail (`object-fit: cover`) | `600 × 600 px` (or `400 × 400 px`) | `300 × 300 px` | `1:1` (Square) | `600 × 600 px` `.webp` | `< 80 KB` |
| **Deals & Promo Cards** | Admin → Deals & Coupons | ~`360 × 200 px` card banner on desktop grid (`object-fit: cover`) | `960 × 540 px` (or `800 × 450 px`) | `640 × 360 px` | `16:9` (Landscape) | `960 × 540 px` `.webp` | `< 150 KB` |
| **Gallery Photos** | Admin → Gallery → *Photo Upload* | ~`360 × 200 px` card (`object-fit: cover`) + Lightbox up to `900 px` wide / `70vh` tall (`object-fit: contain`) | `1280 × 720 px` or `1200 × 800 px` | `800 × 533 px` | `16:9` or `3:2` (Landscape) | `1280 × 960 px` `.webp` | `< 250 KB` |

---

## 2. Automatic Server Optimization & Upload Limits

* **Accepted Formats:** `JPEG` (`.jpg`/`.jpeg`), `PNG` (`.png`), and `WebP` (`.webp`) validated on upload via MIME inspection (`finfo`) and `getimagesize()`.
  * Convert iPhone `HEIC` photos or camera RAW files to `JPEG` or `WebP` before uploading.
  * Do not upload `GIF`, `SVG`, or `BMP` files.
* **Maximum File Size:** `5 MB` per image (`UPLOAD_MAX_BYTES=5242880`). Keep Hostinger/PHP `upload_max_filesize` and `post_max_size` at `8M` or higher.
* **Automatic Processing (`UploadService`):** Every image uploaded to **Sliders**, **Menu**, **Deals**, or **Gallery** is automatically:
  1. **Orientation-corrected** from JPEG EXIF metadata (prevents sideways phone photos).
  2. **Proportionally downscaled** if larger than the section's max dimensions above (smaller images are **never** upscaled).
  3. **Converted to `.webp`** (quality `82`) via PHP GD (with Imagick fallback), stripping heavy camera metadata.
* **Storage & Caching:** Optimized `.webp` files are saved outside the web root in `/storage/uploads/{folder}/` (with PHP execution disabled) and served through `/media/{folder}/{file}` with `Cache-Control: public, max-age=31536000, immutable` (1-year browser caching).

---

## 3. Framing, Cropping & Contrast Tips

### Home Hero Sliders
* Hero banners scale to fill `100%` width at `490px+` height (`object-fit: cover`). Wide monitors trim the **top/bottom** edges, while mobile phones trim the **left/right** edges.
* Keep the main dish or dining scene **centered within the middle 60%** of the photo.
* **Never bake headline text into the image.** Use the CMS `Headline`, `Subtitle`, and `Button` fields so text stays crisp, responsive, and SEO-indexable.
* Choose **darker, warm, high-contrast photography** so the gold and cream overlay text remains easy to read over the gradient backdrop.
* Leaving the image blank is supported and displays the branded dark-brown/gold gradient automatically.

### Menu Dish Photos
* Frame dishes squarely (`1:1`) with the plate centered and slight breathing room around the rim.
* Avoid very wide panoramic plates where toppings sit near the left/right edges, as `84 × 84 px` square cropping trims excess sides equally.

### Deals & Promo Cards
* Use `Title`, `Discount Badge Label`, and `Coupon Code` fields rather than designing text borders onto the promo image. Because `.card-img` renders at `200px` height with `object-fit: cover`, text sitting near top or bottom edges can be clipped on narrow screens.

### Gallery Photos
* Stick to consistent **landscape (`16:9` or `3:2`)** shots for a clean, balanced 3-column grid. Portrait images crop heavily in the `200px` card preview, though clicking opens the full uncropped image inside the Lightbox (`object-fit: contain`).
* Always fill in **Alt Text** (plus **Title** and **Caption** when helpful) for accessibility and search indexing.

---

## 4. Video Guidelines (Gallery YouTube Facades)

To protect page load speed and mobile Lighthouse scores, **do not upload `.mp4` or `.mov` video files directly**. Host videos on YouTube and embed them via **Admin → Gallery → Item Type: `YouTube Video`**.

### Supported YouTube Inputs
Paste any of the following into **YouTube Video URL or ID**:
* Standard watch URL: `https://www.youtube.com/watch?v=VIDEO_ID`
* Short share link: `https://youtu.be/VIDEO_ID`
* Shorts URL: `https://www.youtube.com/shorts/VIDEO_ID`
* Embed URL: `https://www.youtube.com/embed/VIDEO_ID`
* 11-character Video ID only: `VIDEO_ID`

### Video Production & Publishing Checklist
* **Aspect Ratio & Resolution:** Upload horizontal **16:9 HD (`1280 × 720 px`)** or **Full HD (`1920 × 1080 px`)** video to YouTube so playback fills desktop and mobile lightboxes without sidebars.
* **Visibility Setting:** Set the video to **Public** or **Unlisted** on YouTube (never *Private*).
* **Embedding Permission:** In YouTube Studio → Video Details → Show More, verify **Allow embedding** is checked.
* **Thumbnail Preview:** The public Gallery renders a fast `210px`-high facade card using YouTube's thumbnail (`https://img.youtube.com/vi/{VIDEO_ID}/hqdefault.jpg`, `480 × 360 px`) with `loading="lazy"`, and only loads the YouTube `<iframe>` when clicked. Upload a crisp custom `1280 × 720 px` (`16:9`) thumbnail in YouTube Studio for best results.
* **CMS Metadata:** Add a descriptive **Title**, **Caption**, **Category** (`Food`, `Interior`, or `Events`), and **Alt Text** in the CMS so the facade button is accessible and descriptive.

