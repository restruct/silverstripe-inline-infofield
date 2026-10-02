import { test, expect, inlineInfo, isDisplayed, labelOf, openFixturePage, saveDraft } from './support';

// InfoField (a styled info box in the form) and InlineInfoField (an icon that the module's script
// moves into another field's label, showing its text on hover, or on tap on a touch device), in the
// CMS page editor, with the script and stylesheet the module adds to every CMS screen.

test('InfoField renders its HTML in a styled info box, for string and object content', async ({ page }) => {
    const form = await openFixturePage(page);

    const box = form.locator('div.message.info.iib-string');
    await expect(box).toHaveCount(1);
    await expect(box.locator('b.iib-bold')).toHaveText('blocks');
    // Placed before Title, as asked.
    const order = await form.locator('#Root_Main > *').evaluateAll((els) => els.map((e) => e.id || e.className));
    expect(order.findIndex((x) => x.includes('iib-string'))).toBeLessThan(order.indexOf('Form_EditForm_Title_Holder'));

    // client/dist/css/InlineInfoField.css is exposed and loaded: the box gets its icon and colours,
    // and the icon image itself loads (a 404 would be a console error).
    const style = await box.evaluate((el) => {
        const cs = getComputedStyle(el);
        return { image: cs.backgroundImage, colour: cs.backgroundColor, paddingLeft: cs.paddingLeft };
    });
    expect(style.image).toContain('/silverstripe-inline-infofield/client/images/info-icon.png');
    expect(style.colour).toBe('rgb(238, 254, 243)');
    expect(style.paddingLeft).toBe('30px');

    // Object content (a DBHTMLText) is boxed the same way (3.0.0: it used to be output bare).
    const objectBox = form.locator('div.message.info.iib-object');
    await expect(objectBox.locator('i.iib-italic')).toHaveText('Rendered');
});

test('InlineInfoField\'s icon is moved into the label of the field it names, its text hidden', async ({ page }) => {
    const form = await openFixturePage(page);

    // Pushed at the end of the form, shown inside the Title label.
    const info = inlineInfo(form, 'Title');
    await expect(info).toHaveCount(1);
    await expect(labelOf(form, 'Title').locator('span.inline-info[data-target="Title"]')).toHaveCount(1);
    await expect(info).toHaveClass(/\bprocessed\b/);
    await expect(labelOf(form, 'Title')).toHaveClass(/\bhas-inline-infofield\b/);

    // The second one goes to its own label.
    await expect(labelOf(form, 'MenuTitle').locator('span.inline-info[data-target="MenuTitle"]')).toHaveCount(1);

    // Only the icon shows until the editor asks for the text.
    expect(await isDisplayed(info.locator('> span'))).toBe(false);
    expect(await info.evaluate((el) => getComputedStyle(el).backgroundImage)).toContain('/client/images/info-icon.png');
});

test('hovering the icon shows the help text; moving away hides it again', async ({ page }) => {
    const form = await openFixturePage(page);
    const info = inlineInfo(form, 'Title');
    await info.hover();
    await expect(info).toHaveClass(/\bshow\b/);
    expect(await isDisplayed(info.locator('> span'))).toBe(true);
    await expect(info.locator('> span')).toContainText('Keep titles short');
    await expect(info.locator('> span em')).toHaveText('short');

    await form.locator('input[name="Title"]').hover();
    await expect(info).not.toHaveClass(/\bshow\b/);
    expect(await isDisplayed(info.locator('> span'))).toBe(false);

    // A click (on a device without touch) does not toggle it and does not follow the label to its input.
    await info.click();
    await expect(form.locator('input[name="Title"]')).not.toBeFocused();
});

test('after a save re-renders the form, each icon is moved into its label again, once', async ({ page }) => {
    const form = await openFixturePage(page, 'Info fields saved');
    await form.locator('input[name="MenuTitle"]').fill('Saved once');
    await saveDraft(page);

    const reloaded = page.locator('form#Form_EditForm');
    await expect(reloaded.locator('input[name="MenuTitle"]')).toHaveValue('Saved once');
    await expect(labelOf(reloaded, 'Title').locator('span.inline-info')).toHaveCount(1);
    await expect(labelOf(reloaded, 'MenuTitle').locator('span.inline-info')).toHaveCount(1);
    await expect(reloaded.locator('span.inline-info')).toHaveCount(2);
});

test.describe('on a touch device', () => {
    test.use({ hasTouch: true });

    test('a tap toggles the help text', async ({ page }) => {
        const form = await openFixturePage(page);
        expect(await page.evaluate(() => 'ontouchstart' in document.documentElement)).toBe(true);
        const info = inlineInfo(form, 'Title');

        await info.tap();
        await expect(info).toHaveClass(/\bshow\b/);
        expect(await isDisplayed(info.locator('> span'))).toBe(true);
        await info.tap();
        await expect(info).not.toHaveClass(/\bshow\b/);
        expect(await isDisplayed(info.locator('> span'))).toBe(false);
    });
});
