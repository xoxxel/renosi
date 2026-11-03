# Design System Report: Playground – Radix Themes

## 🎨 Color Palette

### Gray Scale (Neutrals)

```css
--gray-1: var(--slate-1)
--gray-2: var(--slate-2)
--gray-3: var(--slate-3)
--gray-4: var(--slate-4)
--gray-5: var(--slate-5)
--gray-6: var(--slate-6)
--gray-7: var(--slate-7)
--gray-8: var(--slate-8)
--gray-9: var(--slate-9)
--gray-10: var(--slate-10)
--gray-11: var(--slate-11)
--gray-12: var(--slate-12)

--gray-a1: var(--slate-a1)
--gray-a2: var(--slate-a2)
--gray-a3: var(--slate-a3)
--gray-a4: var(--slate-a4)
--gray-a5: var(--slate-a5)
--gray-a6: var(--slate-a6)
--gray-a7: var(--slate-a7)
--gray-a8: var(--slate-a8)
--gray-a9: var(--slate-a9)
--gray-a10: var(--slate-a10)
--gray-a11: var(--slate-a11)
--gray-a12: var(--slate-a12)

--gray-surface: var(--slate-surface)
--gray-indicator: var(--slate-indicator)
--gray-track: var(--slate-track)
--gray-contrast: var(--slate-contrast)
```

### Accent Colors

```css
--accent-1: var(--yellow-1)
--accent-2: var(--yellow-2)
--accent-3: var(--yellow-3)
--accent-4: var(--yellow-4)
--accent-5: var(--yellow-5)
--accent-6: var(--yellow-6)
--accent-7: var(--yellow-7)
--accent-8: var(--yellow-8)
--accent-9: var(--yellow-9)
--accent-10: var(--yellow-10)
--accent-11: var(--yellow-11)
--accent-12: var(--yellow-12)

--accent-a1: var(--yellow-a1)
--accent-a2: var(--yellow-a2)
--accent-a3: var(--yellow-a3)
--accent-a4: var(--yellow-a4)
--accent-a5: var(--yellow-a5)
--accent-a6: var(--yellow-a6)
--accent-a7: var(--yellow-a7)
--accent-a8: var(--yellow-a8)
--accent-a9: var(--yellow-a9)
--accent-a10: var(--yellow-a10)
--accent-a11: var(--yellow-a11)
--accent-a12: var(--yellow-a12)

--accent-contrast: var(--yellow-contrast)
--accent-surface: var(--yellow-surface)
--accent-indicator: var(--yellow-indicator)
--accent-track: var(--yellow-track)
```

---

## 📏 Spacing & Layout

```css
--scaling: 1

--space-1: calc(4px * var(--scaling))
--space-2: calc(8px * var(--scaling))
--space-3: calc(12px * var(--scaling))
--space-4: calc(16px * var(--scaling))
--space-5: calc(24px * var(--scaling))
--space-6: calc(32px * var(--scaling))
--space-7: calc(40px * var(--scaling))
--space-8: calc(48px * var(--scaling))
--space-9: calc(64px * var(--scaling))
```

### Scaling Factor

```css
--scaling: 1
```

| Token | Value | Base |
|---|---|---:|
| `--space-1` | `calc(4px * var(--scaling))` | 1 |
| `--space-2` | `calc(8px * var(--scaling))` | 2 |
| `--space-3` | `calc(12px * var(--scaling))` | 3 |
| `--space-4` | `calc(16px * var(--scaling))` | 4 |
| `--space-5` | `calc(24px * var(--scaling))` | 5 |
| `--space-6` | `calc(32px * var(--scaling))` | 6 |
| `--space-7` | `calc(40px * var(--scaling))` | 7 |
| `--space-8` | `calc(48px * var(--scaling))` | 8 |
| `--space-9` | `calc(64px * var(--scaling))` | 9 |

---

## 📐 Borders & Shapes

```css
--radius-1: calc(3px * var(--scaling) * var(--radius-factor))
--radius-2: calc(4px * var(--scaling) * var(--radius-factor))
--radius-3: calc(6px * var(--scaling) * var(--radius-factor))
--radius-4: calc(8px * var(--scaling) * var(--radius-factor))
--radius-5: calc(12px * var(--scaling) * var(--radius-factor))
--radius-6: calc(16px * var(--scaling) * var(--radius-factor))

--radius-factor: 1.5
--radius-full: 9999px
--radius-thumb: 9999px
```

---

## 🖋️ Typography

```css
--font-family: -apple-system, BlinkMacSystemFont, "Segoe UI (Custom)", Roboto, "Helvetica Neue", "Open Sans (Custom)", system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji"

--default-size: 16px
```

### Font Family

```css
-apple-system,
BlinkMacSystemFont,
"Segoe UI (Custom)",
Roboto,
"Helvetica Neue",
"Open Sans (Custom)",
system-ui,
sans-serif,
"Apple Color Emoji",
"Segoe UI Emoji"
```

### Default Size

```css
16px
```

---

## 💡 Usage in Components (Cards)

To maintain consistency in cards:

1. **Background**
   ```css
   var(--gray-surface)
   ```
   or
   ```css
   var(--color-background)
   ```

2. **Padding**
   ```css
   var(--space-3)
   ```
   or
   ```css
   var(--space-4)
   ```

3. **Border Radius**
   ```css
   var(--radius-3)
   ```
   or
   ```css
   var(--radius-4)
   ```

4. **Border**
   ```css
   1px solid var(--gray-a3)
   ```