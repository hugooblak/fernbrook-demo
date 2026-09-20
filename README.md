# Fernbrook Dog Academy — WordPress set up for AI search (GEO/AEO)

A live demo built for an Upwork job: *WordPress – AI Search Implementation*, for a
puppy-development and dog-training business.

**Fernbrook Dog Academy is a fictional business, and Alder Bay is a fictional town.**
Every name, address, price, credential and class date on the site is sample content written
for this demo. There are no reviews and no ratings anywhere on the site, on purpose.

[Open the live demo](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/hugooblak/fernbrook-demo/main/blueprint.json)
— it runs WordPress in your browser, takes about a minute to build itself, and you are logged
in as an administrator.

---

## What this demo is actually showing

The pages are a normal small local-service site. The work is the layer underneath, which is a
plugin: **Fernbrook AI Search**.

### One entity graph per page

Every page prints a single JSON-LD script holding a `@graph` — not five separate blocks that
happen to be on the same page. Each node has a stable `@id` and points at the others.

| Stable `@id` | What it is |
|---|---|
| `/#organization` | The company. Publisher of everything. |
| `/#localbusiness` | The place people visit: address, hours, phone, areas served. |
| `/#website` | The website itself. |
| `/#person-dana-whitlock` | A trainer. The same node is the author of every guide she writes. |
| `/#service-puppy-foundations` | The service the business sells. |
| `/#course-puppy-foundations` | The taught course, with its dates as `CourseInstance`s. |
| `/#place-venue` | The hall. Every class `Event` points at this one `Place`. |
| `/about/#webpage` | A page. Page-level nodes hang off the page address. |

Business entities hang off the site root so renaming a page never breaks them. Page entities
hang off the page address.

Types used: `Organization`, `LocalBusiness`, `WebSite`, `Person`, `Service`, `Course`,
`CourseInstance`, `EducationEvent`, `Article`, `FAQPage`, `ContactPage`, `AboutPage`,
`CollectionPage`, `BreadcrumbList`, `Place`, `ImageObject`, `Offer`, `VideoObject`.

### Everything else in the plugin

- **Business profile** — the facts typed once. The graph, the Open Graph tags and `llms.txt`
  all read from it, so a changed phone number is one edit.
- **Per-page panel** — schema type, title, meta description, canonical, noindex, price, weeks,
  audience, author, video.
- **FAQ read off the page** — the plugin parses the page's own Details blocks. Structured data
  and visible content cannot drift apart.
- **Class dates** — one record per course start produces both the visible list and the Event
  data.
- **Validate** — twelve checks per page, run inside WordPress, each with the exact fix, plus
  one-click links to the Schema.org validator and Google's Rich Results Test.
- **Crawlers** — a row per AI crawler in `robots.txt` (GPTBot, OAI-SearchBot, ChatGPT-User,
  ClaudeBot, Claude-SearchBot, Claude-User, PerplexityBot, Perplexity-User, Google-Extended,
  Applebot-Extended, Amazonbot, meta-externalagent, CCBot, Bytespider), each with a line saying
  what it does.
- **`/llms.txt`** — generated from the pages, so it cannot go stale.
- **Redirects** — old address to new, CSV import, hit counter, and a check for chains and loops.
- **Headings and links** — heading outline per page (one H1, no skipped levels), internal link
  counts, orphan pages, and a page weight estimate.
- **Search Console** — verification field printed in the head of every page.

### The twelve checks

1. One H1 · 2. Heading levels in order · 3. Title length · 4. Meta description length ·
5. Canonical · 6. Open to search engines · 7. JSON-LD is valid JSON · 8. Every node has a
`@type` and an `@id` · 9. No duplicate `@id` · 10. Every `@id` reference resolves ·
11. FAQ answers are on the page · 12. No invented ratings or reviews.

---

## Honest limits

- **The client's site runs Divi. This demo is a block theme.** All of the AI-search work is in a
  plugin and touches no theme markup, so it drops into a Divi site unchanged — but this demo
  does not prove Divi specifically.
- **Search Console cannot show real data in a sandbox.** The verification field and the
  instructions are here; the numbers need a real verified domain and a few days.
- **No `VideoObject` is published.** The fields are built in and switch on the moment all four
  are filled. An invented or empty one is worse than none.
- **No rating or review data anywhere.** This is a demo with no real reviews. Check 12 fails on
  purpose if any ever appears.
- **Page weight is an estimate**, not a field measurement — use PageSpeed Insights for that.
- **The map coordinates are rounded sample values**, because the town is invented. On a real
  site they come from the client's own Google Business Profile pin.
- Class dates are generated relative to the day the demo is built, so they are never stale.

---

## Where to look in the demo

1. **AI Search → Validate** — twelve checks on every page, and the JSON-LD each one publishes.
2. **AI Search → Overview** — the stable `@id` values the site uses.
3. **AI Search → Crawlers and llms.txt** — the policy, and what `robots.txt` and `llms.txt` say.
4. **AI Search → Redirects** — try adding `/puppy-class/` in the address bar.
5. **AI Search → Headings and links** — the outline of every page and the orphan report.
6. **Classes** — edit a date and watch it change on the page *and* in the Event data.
7. **`/?notes=1`** — a note above each section explaining the decision behind it.

## Building it

```bash
tools/wp-local.sh fernbrook 8081
node tools/test-all.mjs fernbrook 8081
```

All 11 checks pass: markup, pages load, link crawl, editor blocks, editor font sizes,
accessibility (axe, WCAG 2.2 AA at 1440 and 390 px), keyboard, forms, layout 320–1440 px,
Lighthouse mobile, screenshots.
