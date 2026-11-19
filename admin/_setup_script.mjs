export default async function run(page, ui) {
  await ui.fill('@e1', 'trinidadroche702@gmail.com');
  await ui.fill('@e2', 'TestAdminPass123');
  await Promise.all([page.waitForURL(/index\.php/, { timeout: 10000 }).catch(() => {}), ui.click('@e3')]);
  await page.waitForTimeout(800);
  return { url: page.url(), body: (await page.locator('body').innerText()).slice(0, 400) };
}
