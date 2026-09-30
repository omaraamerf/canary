# Code architecture

The application follows a small Laravel service-oriented structure. The goal is to keep HTTP concerns, business workflows, and reusable domain values separate without adding layers that do not carry a responsibility.

## Responsibilities

- `app/Http/Controllers`: coordinate the request, call a service, and return a response or view.
- `app/Http/Requests`: authorize and validate incoming HTTP data. Request normalization belongs in `prepareForValidation()`.
- `app/Services`: own business workflows, transactions, state changes, and operations spanning multiple models.
- `app/Enums`: define finite domain values such as order, approval, listing, user, and article statuses.
- `app/Support`: contains focused reusable utilities. `UniqueSlug` is the shared slug generator; avoid global helper functions.
- `app/Models`: define persistence, relationships, casts, accessors, and query scopes.

## Flow

```text
Route -> Form Request -> Controller -> Service -> Model/database
                                  -> Response/view
```

Controllers may query data needed only to render an index or edit page. A workflow that changes several records, requires a transaction, or is shared by multiple controllers belongs in a service.

## Current services

- `OrderService`: creates reservations and updates order/bird states atomically.
- `BirdService`: saves admin and seller listings and synchronizes their media.
- `SellerService`: registers sellers and updates seller profiles and approval states.
- `ArticleService`: saves guide articles and synchronizes tags.
- `CatalogService`: saves regions, breeds, and guide categories with unique slugs.
- `SettingService`: reads and updates the marketplace feature settings.
- `CloudinaryMediaService`: signs image/video uploads and deletes Cloudinary assets by public ID.
- `CommunityService`: publishes community posts and comments with their polymorphic media, accepts solutions, and moderates visibility.
- `MemberService`: registers public member accounts (role `member`) that can post and comment but cannot access any panel.

Do not add a service or helper for a one-line model operation unless it represents a domain rule or is reused. Prefer backed enums over repeating status strings in PHP business logic.
