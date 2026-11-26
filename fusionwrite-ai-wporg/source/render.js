// node render.js <html> <width> <height> <frames> <outDir> [scale]
const { chromium } = require('playwright');
const fs = require('fs');
const [html, W, H, N, out, scale] = process.argv.slice(2);
(async () => {
  fs.mkdirSync(out, { recursive: true });
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: +W, height: +H }, deviceScaleFactor: +(scale || 1) });
  await p.goto('file://' + process.cwd() + '/' + html);
  await p.evaluate(() => document.fonts.ready);
  await p.waitForTimeout(300);
  for (let i = 0; i < +N; i++) {
    await p.evaluate(t => window.renderFrame(t), i / +N);
    await p.screenshot({ path: `${out}/f${String(i).padStart(3, '0')}.png` });
  }
  await b.close();
})();
