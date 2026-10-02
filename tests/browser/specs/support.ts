import { test as base, expect, type Locator, type Page, type Request } from '@playwright/test';

// Shared fixtures and helpers for the inline-infofield specs. The CMS screen is the page editor for
// a fixture page (fixtures/IiBPage.php) whose form has two InfoFields and two InlineInfoFields.

/**
 * test, extended with an automatic guard: every spec fails if the page logs a console error (a
 * failed request, such as a missing module stylesheet or icon, included), throws an uncaught
 * exception, or opens a dialog. Warnings do not count.
 */
export const test = base.extend<{ guard: void }>({
    guard: [
        async ({ page }, use, testInfo) => {
            const errors: string[] = [];
            page.on('console', (msg) => {
                if (msg.type() === 'error') {
                    errors.push(`console.error: ${msg.text()} (${msg.location().url})`);
                }
            });
            page.on('pageerror', (err) => errors.push(`uncaught: ${err.message} | ${(err.stack ?? '').split('\n').slice(1, 3).join(' | ').trim()}`));
            page.on('dialog', async (dialog) => {
                if (dialog.type() !== 'beforeunload') {
                    errors.push(`unexpected ${dialog.type()}(): ${dialog.message()}`);
                }
                await (dialog.type() === 'beforeunload' ? dialog.accept() : dialog.dismiss());
            });

            await use();

            if (errors.length) {
                await testInfo.attach('console-errors', { body: errors.join('\n'), contentType: 'text/plain' });
            }
            expect(errors, 'no console errors, uncaught exceptions or dialogs').toEqual([]);
        },
        { auto: true },
    ],
});

export { expect };

/** Open the fixture page (created on first use) in the CMS page editor with a full page load. */
export async function openFixturePage(page: Page, title = 'Info fields'): Promise<Locator> {
    const response = await page.request.get('/admin/iib-reset/reseed', { params: { title } });
    expect(response.status()).toBe(200);
    const { id } = await response.json();
    await page.goto(`/admin/pages/edit/show/${id}`);
    const form = page.locator('form#Form_EditForm');
    await expect(form.locator('input[name="Title"]')).toBeVisible();
    return form;
}

/** An InlineInfoField's icon, by the name of the field it is aimed at. */
export function inlineInfo(form: Locator, target: string): Locator {
    return form.locator(`span.inline-info[data-target="${target}"]`);
}

/** The label of a field in the page editor. */
export function labelOf(form: Locator, name: string): Locator {
    return form.locator(`#Form_EditForm_${name}_Holder label`).first();
}

/** Save the page and wait for the CMS's AJAX save, after which it swaps in the re-rendered form. */
export async function saveDraft(page: Page): Promise<Request> {
    const posted = page.waitForRequest((r) => r.method() === 'POST' && /\/admin\/pages\/edit\/EditForm/.test(r.url()));
    await page.locator('button[name="action_save"]').click();
    const request = await posted;
    expect(['xhr', 'fetch']).toContain(request.resourceType());
    expect((await request.response())?.status(), 'save answered 200').toBe(200);
    return request;
}

/** Whether an element is displayed (the help text is shown and hidden with CSS display). */
export async function isDisplayed(locator: Locator): Promise<boolean> {
    return locator.evaluate((el) => getComputedStyle(el).display !== 'none');
}
