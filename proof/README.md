# Test proof — Fernbrook Dog Academy

Run on 2026-09-20 against a fresh WordPress install with the demo content. Local test server, no compression.

| Check | Result | Details |
|---|---|---|
| 1. Markup lint | ✅ Pass | clean |
| 2. Pages load | ✅ Pass | 12 pages |
| 3. Link crawl | ✅ Pass | 61 URLs checked |
| 4. Editor: no invalid blocks | ✅ Pass | 16 posts/pages, 22 patterns, all templates |
| 5. Editor font sizes = live site | ✅ Pass | 25 text elements compared |
| 6. Accessibility (axe, WCAG 2.2 AA) | ✅ Pass | 12 pages × 2 widths, 0 violations |
| 7. Keyboard | ✅ Pass | skip link, focus outline, FAQ, mobile menu |
| 8. Forms | ✅ Pass | 6 forms: every field labelled (CF7: errors shown) |
| 9. Layout 320–1440px | ✅ Pass | no overflow, header fits |
| 10. Lighthouse (mobile) | ✅ Pass | / 97/100/100/100  /puppy-foundations/ 98/100/100/100  /adolescent-manners/ 98/100/100/100  /private-training/ 98/100/100/100  /classes/ 99/100/100/100  /about/ 99/100/100/100 |
| 11. Screenshots | ✅ Pass | 33 saved to proof/screenshots |

## Lighthouse (mobile)

| Page | Speed | Accessibility | Best practices | SEO | LCP | CLS |
|---|---|---|---|---|---|---|
| / | 97 | 100 | 100 | 100 | 2.4 s | 0 |
| /puppy-foundations/ | 98 | 100 | 100 | 100 | 2.3 s | 0 |
| /adolescent-manners/ | 98 | 100 | 100 | 100 | 2.3 s | 0 |
| /private-training/ | 98 | 100 | 100 | 100 | 2.1 s | 0 |
| /classes/ | 99 | 100 | 100 | 100 | 2.0 s | 0 |
| /about/ | 99 | 100 | 100 | 100 | 1.9 s | 0 |

Automated tests cannot catch every accessibility or design problem. A real project adds manual screen reader testing.
