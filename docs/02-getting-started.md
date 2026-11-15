Build a production-quality website for "PulchraNails", a personal nail art atelier in Katapang, 
Kabupaten Bandung, Indonesia. Work in stages A to E. After each stage, run the app/build, fix errors, 
and summarize what works before continuing.

STACK
Next.js (App Router) + TypeScript + Tailwind CSS, Prisma ORM, PostgreSQL (DATABASE_URL from env), 
Auth.js (credentials provider) for admin login, Zod for validation, Framer Motion for animation, 
next/font for Playfair Display and DM Sans. Image uploads: store on object storage (Supabase Storage or Cloudinary, 
configurable by env). No secrets in code; provide .env.example.

DESIGN (match the attached screenshots as closely as possible)
Tokens: background #f5eded; cards/navbar #fcf8f6 with 1px pink hairline border and soft shadow; 
dark plum #2a141c (text, primary buttons); burgundy #7d3550 (accent, prices, italic words, eyebrows); 
table header #f0e0e0; muted mauve body text.
Headings: Playfair Display, very large, letter-spacing -3%, line-height ~0.95, one key word per heading 
in italic burgundy. Body/buttons: DM Sans. Eyebrows: uppercase, wide tracking, small, preceded by a short burgundy line.
Buttons are full pills. Cards radius ~28px. Photo and gallery cards use an ARCH shape 
(top radius 999px, bottom 24px). Floating pill navbar (cream, rounded-full, shadow): logo left, 
links Layanan, Galeri, Press-On, Kunjungan, Lacak (active = light pink pill), right dark pill "MULAI ORDER ↗".
Thin burgundy scroll-progress bar at top; fade-up on scroll; respect prefers-reduced-motion. 
Footer: darker blush band, 3 columns. Fully responsive. Accessible (semantic HTML, alt text, focus states, contrast).
Public copy in casual Indonesian.

STAGE A: PROJECT + DATA
- Initialize the project, lint/format, folder structure, layout with navbar/footer components.
- Prisma schema: User(role: SUPER_ADMIN | SUB_ADMIN, active, passwordHash, canSeeOnlyAssigned), 
  Service(name, priceReal, priceFake nullable, unit, category, isAddon, active, sortOrder), 
  GalleryItem(title, category: PLAIN|NAIL_ART|EXTENSION|PRESS_ON, imageUrl, published, sortOrder), 
  PressOnTier(name, price, description, sortOrder), HomeServiceRate(people, price), 
  Order(code unique "PN-XXXX", customerName, whatsapp, type: STUDIO|HOME, address nullable, preferredDate, 
  preferredTime, peopleCount, notes, colors, referenceLinks, status enum 
  [MENUNGGU, DIKONFIRMASI, DIJADWALKAN, DIKERJAKAN, SELESAI, DIBATALKAN], estimatedTotal, finalTotal nullable, 
  customerNote, internalNote, assignedToId nullable, promoCode nullable), OrderItem(orderId, serviceId, nailType REAL|FAKE, price), 
  OrderImage(orderId, url), Promo(code, description, type PERCENT|FIXED, value, validFrom, validUntil, usageLimit, usedCount, 
  active, showOnLive), BlockedDate(date, reason), Setting(key, value), ActivityLog(userId, action, entity, entityId, meta, createdAt).
- Seed script (idempotent) with these services (price real / fake / unit):
  Manicure Only 50.000 / — / set; Pedicure Only 60.000 / — / set; Plain Gel Polish Hands 65.000 / 115.000 / set 10 kuku; 
  Plain Gel Polish Feet 80.000 / 130.000; Glitter-Lines-Dots 85.000 / 135.000; Cat Eye 90.000 / 140.000; 
  Chrome-Aurora 95.000 / 145.000; French 95.000 / 145.000; Ombre Blush 90.000 / 140.000; 
  Soft-Gel-Tip Extension add-on +50.000. Home service rates: 1 orang 10.000, 2 orang 15.000, 3 orang 20.000.
  Gallery seeds: Plain Gloss (PLAIN), Floral Art (NAIL_ART), Chrome Aurora (NAIL_ART). 
  Super admin created from ADMIN_EMAIL and ADMIN_PASSWORD env vars.

STAGE B: PUBLIC PAGES (server components, data from DB)
/ (hero with arch photo + offset outline arch + "STYLED VISUAL" badge + "VISUAL DIRECTION / 01 — Cherry bloom" caption; 
marquee; intro; 3 numbered menu cards; gallery teaser; press-on teaser; visit teaser; process steps; 
review-themes section; final CTA), /services (table: Layanan | Real | Fake | Unit, Rupiah formatting), 
/gallery (filter chips Semua, Plain, Nail Art, Extension, Press-On, arch cards, detail modal with 
"Order dengan referensi ini"), /press-on, /visit (studio card + home service rates card), /live (active promo), 
custom 404/500. Per-page metadata, Open Graph, sitemap.xml, robots.txt.
Exact copy for hero: eyebrow "NAIL ATELIER · KATAPANG, BANDUNG"; heading "Little details, *big feeling.*"; 
paragraph "Nail art yang terasa seperti kamu. Bawa warna, ide, atau inspirasi favoritmu—lalu kita mulai merancang detail kecil 
yang ingin kamu lihat setiap hari."; buttons "Mulai order ↗" and "Lihat menu & harga". 
Visit address: Kp. Junti Girang No. 01/10, Banyusari, Kec. Katapang, Kabupaten Bandung, Jawa Barat 40921.

STAGE C: ORDER + TRACKING
- /order: 3-step form (Layanan, Preferensi, Kontak) with progress indicator, multiple services, Real/Fake, add-on, 
  studio/home, date/time (respect BlockedDate and working hours), people count for home logistics fee, 
  up to 3 reference images (type and size checked, max 5MB), links, colors, notes, promo code, 
  live estimated total labelled "Estimasi, total final dikonfirmasi admin".
- Server action/API: validate with Zod, normalize WhatsApp to 62..., recompute prices ON THE SERVER (never trust client totals), 
  generate unique code PN-XXXX (uppercase, no ambiguous characters), status MENUNGGU, 
  show success page with code and a wa.me button prefilled with the order summary.
- /track: enter code, then verify the WhatsApp number on the order before showing details (status timeline, items, 
  schedule, estimated/final total, customer note). Generic error for wrong code or number. 
  Rate limit (5 failed attempts per IP then 15 min lock).

STAGE D: ADMIN (/admin, separate login at /admin/login)
- Auth.js credentials, argon2/bcrypt, httpOnly secure cookies, login rate limit.
- Roles enforced on the SERVER for every route and server action:
  SUPER_ADMIN: everything, including user management, prices, promos, settings, deletion.
  SUB_ADMIN: view and update orders, add notes, change status, manage gallery and calendar; 
  cannot delete orders, edit prices, manage promos/users/settings; optional "only assigned orders" mode.
- Pages: Dashboard (today, pending, week schedule, month revenue from SELESAI orders), Orders (search, filters, pagination, detail page 
  with image viewer, status, assignment, final price, internal/customer notes, WhatsApp template buttons per status, print view), 
  Calendar (day/week, double-booking prevention, blocked dates, working hours), Services & prices, Gallery (upload, publish, reorder), 
  Press-on tiers, Promos, Customers (history, CSV export), Staff (create/deactivate sub admins, reset password, activity log), Settings.
- Activity log for logins, status changes, price changes, deletions. CSV export for orders.

STAGE E: QUALITY + DEPLOY
- Security headers, CSRF protection for mutations, input validation everywhere, upload checks, no sensitive data in public API responses.
- Tests: unit tests for price calculation, order code generation, and role permissions; one e2e flow (create order → track → admin confirm).
- Performance: next/image, lazy loading, caching of public pages with revalidation when admin edits data.
- README in plain Indonesian for a beginner: install Node, set .env, run migrations and seed, run locally, deploy to Vercel, 
  connect a custom domain, and a checklist of environment variables (DATABASE_URL, AUTH_SECRET, ADMIN_EMAIL, ADMIN_PASSWORD, 
  SITE_URL, storage keys, WHATSAPP_ADMIN_NUMBER).
- Leave placeholders for logo and hero image in /public and tell me exactly which files to replace.

RULES
Do not invent business data beyond what is given; use clearly marked placeholders for anything unknown (press-on tiers, opening hours). 
No payment integration. Keep code typed and commented where logic is non-obvious.
