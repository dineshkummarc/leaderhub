## ADDED Requirements

### Requirement: Package Definition
The `packages/design-tokens/package.json` SHALL define package name `@leaderhub/design-tokens` with a `build` script.

#### Scenario: Package is installable
- **WHEN** developer runs `pnpm install` in `packages/design-tokens/`
- **THEN** the command MUST complete without errors

### Requirement: Source of Truth
The `packages/design-tokens/tokens.json` file SHALL be the single source of truth for all design tokens (colors, radius, fonts). It SHALL include both light and dark mode color values.

#### Scenario: Tokens file contains light and dark colors
- **WHEN** developer inspects `tokens.json`
- **THEN** the file MUST contain `color.light` and `color.dark` sections with accent, background, surface, text, and border color values

### Requirement: Build Outputs
Running `pnpm build` SHALL generate three files in the `dist/` directory: `tokens.css` (CSS variables with `:root` for light and `html.dark` for dark), `tokens.js` (Tailwind config object with colors, fontFamily, borderRadius), and `filament-theme.php` (PHP array of Filament panel colors).

#### Scenario: Build produces all three output files
- **WHEN** developer runs `pnpm build` in `packages/design-tokens/`
- **THEN** the `dist/` directory MUST contain `tokens.css`, `tokens.js`, and `filament-theme.php`

#### Scenario: CSS contains light and dark variables
- **WHEN** developer inspects `dist/tokens.css`
- **THEN** the file MUST contain `:root { --color-bg: #FFFFFF; ... }` for light mode and `html.dark { --color-bg: #0B0F14; ... }` for dark mode

### Requirement: Color Palette
The tokens SHALL define the Modern Premium palette: neutral-first (slate-based) colors with accent red `#DC2626` for light mode and `#EF4444` for dark mode.

#### Scenario: Accent color is red
- **WHEN** developer inspects `tokens.json`
- **THEN** `color.light.accent.value` MUST be `#DC2626` and `color.dark.accent.value` MUST be `#EF4444`

### Requirement: Typography
The tokens SHALL define `--font-sans: 'Inter', system-ui, sans-serif` and a `--font-mono` value for monospace text.

#### Scenario: Font sans is Inter
- **WHEN** developer inspects `tokens.json`
- **THEN** `font.sans.value` MUST contain `Inter`

### Requirement: Border Radius
The tokens SHALL define a radius scale: `sm: 6px`, `default: 10px`, `lg: 14px`, `xl: 18px`, `full: 9999px`.

#### Scenario: Radius values are correct
- **WHEN** developer inspects `tokens.json`
- **THEN** `radius.sm.value` MUST be `6px`, `radius.default.value` MUST be `10px`, `radius.lg.value` MUST be `14px`

### Requirement: Consumed by All Apps
The website's `tailwind.config.js` SHALL import `@leaderhub/design-tokens/dist/tokens.js`. The hub and gym Filament panel colors SHALL use values from `dist/filament-theme.php`.

#### Scenario: Website Tailwind uses design tokens
- **WHEN** developer inspects `apps/website/tailwind.config.js`
- **THEN** the colors configuration MUST reference CSS variables from the design tokens (e.g., `var(--color-accent)`)

#### Scenario: Hub Filament uses design token colors
- **WHEN** developer inspects the hub's `HubPanelProvider`
- **THEN** the primary color MUST be `#DC2626` (from design tokens)
