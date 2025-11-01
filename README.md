# 🎥 Defer YouTube Background Video for Elementor 

Improve page speed and LCP (Largest Contentful Paint) by deferring background YouTube videos in Elementor sections.

![WordPress Tested](https://img.shields.io/badge/WordPress-6.5-blue?logo=wordpress)
![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue)
![License](https://img.shields.io/badge/license-GPLv2%2B-blue)

👉 Official project page: [Background Video Delay for Elementor](https://github.com/DavidRe9/background-video-delay-elementor)

> ⚠️ **Notice:** This project was originally created by [@DavidRe9](https://github.com/DavidRe9).  
> It is licensed under GPLv2+, and **must retain proper author attribution**.

---

## 🎬 Live Demo

Experience the Smart Fit Core system in action — see how the background video perfectly adapts to any screen size.

👉 [View the official demo discussion](https://github.com/DavidRe9/background-video-delay-elementor/discussions/2)

> 💡 Note: Demo recorded locally using generic assets — no client data is shown.


## 🚀 Features

- ✅ Delay background YouTube video loading
- ✅ Improve LCP & page performance
- ✅ Full mobile responsiveness
- ✅ Per-page or global targeting
- ✅ YouTube Privacy Mode (`youtube-nocookie`)
- ✅ Admin panel for easy rule management
- ✅ Fallback image support for Elementor Flexbox

---

## 📦 Installation

1. Upload the plugin folder to `/wp-content/plugins/`, or install it via the WordPress dashboard.
2. Activate the plugin.
3. Go to **WP Admin > BG Video Rules** and define your rules.
4. In Elementor, **do not set a background video** — the plugin handles it.

---

## 🧠 Usage Tips

- Use a static background image or color as fallback.
- Use precise CSS selectors like `#hero` or `.video-section`.
- Avoid using multiple video sections on one page for performance reasons.

---

## ❓ FAQ

**Q: Does this work with any theme?**  
**A:** It works with most Elementor-compatible themes, but not all. Compatibility depends on the theme's HTML and CSS structure. Always test before deploying to production.

**Q: Will this help my Core Web Vitals?**  
**A:** Yes, especially for LCP (Largest Contentful Paint), by deferring the loading of background YouTube videos. However, actual results may vary depending on your caching plugins, theme, hosting, and other optimizations.

**Q: Is it compatible with Elementor Flexbox containers?**  
**A:** Yes, and it includes fallback image support in case Flexbox is used on mobile.

**Q: Does it support Vimeo or self-hosted videos?**  
**A:** Currently, only YouTube background videos are supported.

**Q: Can I target specific pages only?**  
**A:** Yes, the plugin includes per-page rules or global targeting options.

---

⚠️ **Note:** While this plugin helps improve performance by deferring video load, best results come when used together with proper caching, CDN, image optimization, and minimal render-blocking elements.

---

## 📄 License

GPLv2 or later – [View License](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ Want to contribute?

## 🧾 License and Attribution

This project is licensed under the **GNU General Public License v2.0 or later (GPLv2+).**

Copyright © [DavidRe9](https://github.com/DavidRe9)

You are free to redistribute and/or modify this code under the following conditions:
- Keep the **original license notice (GPLv2+)**;
- Keep the **original author attribution** to [@DavidRe9](https://github.com/DavidRe9);
- Distribute the **complete source code** as required by the GPL.

Copies, forks, or redistributions that **remove or obscure the original author credit** are considered a violation of the license terms.

Pull requests and issues are welcome!

---

