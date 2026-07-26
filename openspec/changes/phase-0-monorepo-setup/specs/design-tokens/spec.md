# Spec: design-tokens

## Requirements

### REQ-001: Package Definition
`packages/design-tokens/package.json` MUST define package name `@leaderhub/design-tokens` with a `build` script.

### REQ-002: Source of Truth
`packages/design-tokens/tokens.json` MUST be the single source of truth for all design tokens (colors, radius, fonts). It MUST include both light and dark mode color values.

### REQ-003: Build Outputs
Running `pnpm build` MUST generate three files in `dist/`:
- `tokens.css` — CSS variables with `:root` (light) and `html.dark` (dark) selectors
- `tokens.js` — Tailwind config object (colors, fontFamily, borderRadius)
- `filament-theme.php` — PHP array of Filament panel colors

### REQ-004: Color Palette
Tokens MUST define the Modern Premium palette: neutral-first (slate-based) with accent red `#DC2626` (light) / `#EF4444` (dark).

### REQ-005: Typography
Tokens MUST define `--font-sans: 'Inter', system-ui, sans-serif` and `--font-mono` for monospace.

### REQ-006: Border Radius
Tokens MUST define radius scale: `sm: 6px`, `default: 10px`, `lg: 14px`, `xl: 18px`, `full: 9999px`.

### REQ-007: Consumed by All Apps
- Website: `tailwind.config.js` MUST import `@leaderhub/design-tokens/dist/tokens.js`
- Hub & Gym: Filament panel colors MUST use values from `dist/filament-theme.php`