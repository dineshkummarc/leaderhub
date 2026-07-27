## ADDED Requirements

### Requirement: Blog Categories Table
A `blog_categories` table SHALL exist with: `id`, `slug` (UNIQUE), `name`, `description` (nullable), timestamps.

#### Scenario: Category slug is unique
- **WHEN** developer attempts to create two categories with the same slug
- **THEN** the database MUST reject the duplicate

### Requirement: Blog Posts Table
A `blog_posts` table SHALL exist with: `id`, `blog_category_id` (FK cascade), `author_id` (FK to users cascade), `slug` (UNIQUE), `title`, `title_en` (nullable), `excerpt` (nullable), `content` (LONGTEXT), `content_en` (nullable), `featured_image` (nullable), `status` (default draft), `published_at` (nullable), `reading_time` (nullable), `seo_title` (nullable), `seo_description` (nullable), timestamps. Index on `(status, published_at)`.

#### Scenario: Blog post has category and author FK
- **WHEN** developer inspects the blog_posts table
- **THEN** foreign keys to blog_categories (cascade) and users (cascade) MUST exist

### Requirement: Blog Post Tags Pivot
A `blog_post_tags` pivot table SHALL exist with composite primary key `(blog_post_id, blog_tag_id)`. Both columns SHALL have FK with cascadeOnDelete. There SHALL NOT be a separate `id` column.

#### Scenario: Pivot table has composite PK
- **WHEN** developer inspects the blog_post_tags table
- **THEN** the primary key MUST be composite (blog_post_id, blog_tag_id) with no separate id column

### Requirement: BlogPost Global Scope
The `BlogPost` model SHALL have a global scope `published` filtering `status = published` AND `published_at <= now`. This scope SHALL be automatically applied unless removed via `withoutGlobalScope('published')`.

#### Scenario: Draft posts are hidden from frontend
- **WHEN** `BlogPost::all()` is called
- **THEN** only posts with status `published` and `published_at <= now` MUST be returned

#### Scenario: Admin can bypass scope
- **WHEN** `BlogPost::withoutGlobalScope('published')->get()` is called
- **THEN** all posts including drafts MUST be returned

### Requirement: Blog Listing Page
`GET /blog` SHALL display a paginated list (9 per page) of published blog posts with: editorial grid (3 columns desktop), card with image/category/title/meta, pagination preserving query string, and empty state.

#### Scenario: Blog listing paginates at 9 per page
- **WHEN** user navigates to `/blog` with 20 published posts
- **THEN** the page MUST display 9 posts and pagination links

### Requirement: Blog Filter by Category and Tag
`GET /blog?category={slug}` SHALL filter by category. `GET /blog?tag={slug}` SHALL filter by tag. `GET /blog?q={query}` SHALL search by title (LIKE).

#### Scenario: Category filter works
- **WHEN** user navigates to `/blog?category=tips`
- **THEN** only posts belonging to the category with slug `tips` MUST be displayed

### Requirement: Blog Detail Page
`GET /blog/{slug}` SHALL display: breadcrumb, featured image, category badge, H1 title, meta (author/date/reading time), content, tags as clickable badges, and related posts (3, same category, excluding current).

#### Scenario: Blog detail shows related posts
- **WHEN** user views a blog post detail page
- **THEN** up to 3 related posts from the same category MUST be displayed, excluding the current post

### Requirement: Blog Detail JSON-LD
Blog detail page SHALL include JSON-LD Article schema with: headline, author (Person), datePublished (ISO 8601), image, publisher (Organization).

#### Scenario: Article schema is valid
- **WHEN** developer validates the blog detail JSON-LD at validator.schema.org
- **THEN** the Article schema MUST pass validation

### Requirement: Blog Route Order
Routes `/blog` and `/blog/{slug}` SHALL be registered BEFORE the `/{slug}` route (PageController) in `routes/web.php`.

#### Scenario: Blog URL is not captured by page route
- **WHEN** user navigates to `/blog/post-1`
- **THEN** the blog detail page MUST load (not the page controller returning 404)

### Requirement: BlogPostResource
A `BlogPostResource` SHALL exist with: form (title live auto-slug, slug unique, category select with inline create, author select default auth user, tags multi-select with inline create, excerpt, content RichEditor, featured_image FileUpload, status select, published_at DateTimePicker, SEO section collapsible), table (title searchable, category, author, status badge, published_at).

#### Scenario: Admin creates a published blog post
- **WHEN** admin creates a blog post with status `published` and a valid published_at date
- **THEN** the post MUST appear on the website blog listing page

### Requirement: Blog 404
`GET /blog/{nonexistent-slug}` SHALL return HTTP 404.

#### Scenario: Nonexistent blog slug returns 404
- **WHEN** user navigates to `/blog/nonexistent-slug`
- **THEN** the server MUST return HTTP 404
