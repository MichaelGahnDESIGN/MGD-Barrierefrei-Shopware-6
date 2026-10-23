document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-mgd-a11y]');
    if (!root) return;

    const trigger = root.querySelector('.mgd-a11y__trigger');
    const dialog = root.querySelector('.mgd-a11y__dialog');
    const close = root.querySelector('.mgd-a11y__close');
    const key = 'mgd-barrierefrei-shopware';
    const state = JSON.parse(localStorage.getItem(key) || '{}');
    const html = document.documentElement;
    let previouslyFocused = null;

    const apply = () => {
        html.classList.toggle('mgd-a11y--contrast', Boolean(state.contrast));
        html.classList.toggle('mgd-a11y--gray', Boolean(state.gray));
        html.classList.toggle('mgd-a11y--focus', Boolean(state.focus));
        html.classList.toggle('mgd-a11y--motion', Boolean(state.motion));
        html.style.setProperty('--mgd-a11y-font-scale', String(state.scale || 1));
        localStorage.setItem(key, JSON.stringify(state));
    };
    const open = () => { previouslyFocused = document.activeElement; dialog.hidden = false; trigger.setAttribute('aria-expanded', 'true'); dialog.focus(); };
    const hide = () => { dialog.hidden = true; trigger.setAttribute('aria-expanded', 'false'); if (previouslyFocused instanceof HTMLElement) previouslyFocused.focus(); };

    apply();
    trigger.addEventListener('click', open);
    close.addEventListener('click', hide);
    dialog.addEventListener('click', (event) => {
        const action = event.target.closest('[data-mgd-a11y-action]')?.dataset.mgdA11yAction;
        if (!action) return;
        if (action === 'reset') Object.keys(state).forEach((keyName) => delete state[keyName]);
        if (action === 'contrast' || action === 'gray' || action === 'focus' || action === 'motion') state[action] = !state[action];
        if (action === 'font-up') state.scale = Math.min(1.35, Number(state.scale || 1) + 0.1);
        if (action === 'font-down') state.scale = Math.max(0.9, Number(state.scale || 1) - 0.1);
        apply();
    });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !dialog.hidden) hide(); });
});
