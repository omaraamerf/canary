# Cloudinary media setup

Bird images and videos are uploaded from Laravel to Cloudinary using signed server-side requests. The API secret never reaches the browser.

## Environment

Create a Cloudinary product environment, then copy the single environment URL shown by Cloudinary into `.env`:

```dotenv
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

Keep it on one line without quotes or trailing text. Separate values are also supported if preferred:

```dotenv
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
CLOUDINARY_FOLDER=canary/birds
```

After changing production environment variables, clear the cached configuration:

```bash
php artisan config:clear
```

Run the database migration that adds `provider` and `public_id` to `bird_media`:

```bash
php artisan migrate
```

## Upload limits

The application accepts up to eight images and three videos per request. Defaults are 10MB per image and 50MB per video and can be changed with:

```dotenv
CLOUDINARY_IMAGE_MAX_KB=10240
CLOUDINARY_VIDEO_MAX_KB=51200
CLOUDINARY_UPLOAD_TIMEOUT=120
CLOUDINARY_MAX_IMAGES=8
CLOUDINARY_MAX_VIDEOS=3
```

PHP and the web server must allow at least the same total request size. Adjust `upload_max_filesize`, `post_max_size`, and the web server request-body limit in production when videos larger than the server defaults are required.

Existing URL-based media records remain displayable. New uploads store Cloudinary's secure URL and public ID. Removing a Cloudinary-backed item from the edit form also requests deletion of that asset from Cloudinary after the database update succeeds.
