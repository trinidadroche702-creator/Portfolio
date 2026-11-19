export default async function run(page, ui) {
  const snap = await ui.snapshot();
  const email = snap.match(/@(e\d+) textbox "email"/)?.[1];
  const pass = snap.match(/@(e\d+) textbox "password"/)?.[1];
  const btn = snap.match(/@(e\d+) button "Log in"/)?.[1];
  await ui.fill(email, 'roejieadamso@gmail.com');
  await ui.fill(pass, 'TestAdminPass123');
  await Promise.all([page.waitForNavigation({waitUntil:'load'}).catch(()=>{}), ui.click(btn)]);
  await page.waitForTimeout(500);
  return { url: page.url(), title: await page.title(), body: (await page.locator('body').innerText()).slice(0, 300) };
}
