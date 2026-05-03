---
name: Gençlik Rehberim Design System
colors:
  surface: '#fbf9f8'
  surface-dim: '#dbd9d9'
  surface-bright: '#fbf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3f3'
  surface-container: '#efeded'
  surface-container-high: '#eae8e7'
  surface-container-highest: '#e4e2e2'
  on-surface: '#1b1c1c'
  on-surface-variant: '#414751'
  inverse-surface: '#303030'
  inverse-on-surface: '#f2f0f0'
  outline: '#727782'
  outline-variant: '#c1c6d3'
  surface-tint: '#075fab'
  primary: '#075fab'
  on-primary: '#ffffff'
  primary-container: '#5d9cec'
  on-primary-container: '#003260'
  inverse-primary: '#a4c9ff'
  secondary: '#006d43'
  on-secondary: '#ffffff'
  secondary-container: '#93f3bb'
  on-secondary-container: '#007146'
  tertiary: '#8f4e00'
  on-tertiary: '#ffffff'
  tertiary-container: '#de8429'
  on-tertiary-container: '#4e2800'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d4e3ff'
  primary-fixed-dim: '#a4c9ff'
  on-primary-fixed: '#001c39'
  on-primary-fixed-variant: '#004884'
  secondary-fixed: '#95f6be'
  secondary-fixed-dim: '#7adaa3'
  on-secondary-fixed: '#002111'
  on-secondary-fixed-variant: '#005231'
  tertiary-fixed: '#ffdcc2'
  tertiary-fixed-dim: '#ffb77a'
  on-tertiary-fixed: '#2e1500'
  on-tertiary-fixed-variant: '#6d3a00'
  background: '#fbf9f8'
  on-background: '#1b1c1c'
  surface-variant: '#e4e2e2'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '800'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  body-lg:
    fontFamily: Lexend
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Lexend
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Lexend
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  button:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '700'
    lineHeight: 24px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 40px
  xl: 64px
  container-max: 1200px
  gutter: 20px
---

## Brand & Style

The design system is built to bridge the gap between authority and peer-to-peer connection. Targeting students aged 11-14, the visual direction avoids "childish" aesthetics in favor of a **Modern, Approachable, and Empathetic** style. The goal is to create a safe digital space where users feel supported rather than lectured.

The core style combines **Soft Minimalism** with **Tactile Gamification**. By using generous whitespace, soft-touch surfaces, and friendly interactive elements, the UI reduces the anxiety often associated with the topic of bullying. The design language emphasizes "Soft Progress"—using gentle curves and positive reinforcement to guide students through educational content and reporting tools.

## Colors

This design system utilizes a palette designed to balance calmness with active engagement:

- **Pastel Blue (Primary):** Used for primary actions and navigation to instill a sense of trust and tranquility.
- **Soft Green (Secondary):** Applied to success states, growth-oriented content, and "safe zone" indicators.
- **Positive Orange (Tertiary):** A vibrant but non-aggressive tone used for highlights, gamified achievements, and points of interest to maintain high engagement.
- **Semantic Neutrals:** Grays are slightly tinted with blue to keep the interface feeling cool and modern, ensuring high contrast for accessibility (WCAG AA compliant).

## Typography

The typography strategy prioritizes readability and a friendly tone. 

**Plus Jakarta Sans** is used for headlines to provide a modern, geometric, yet soft feel. Its open apertures and friendly curves make the app feel welcoming. 

**Lexend** is utilized for all body copy and labels. Specifically designed to improve reading proficiency, it is ideal for an educational platform for young teens, ensuring that content regarding peer bullying is easily digestible and accessible to readers of all levels.

## Layout & Spacing

The design system employs a **Fluid Grid** with a 12-column structure for desktop and a 4-column structure for mobile. 

The rhythm is based on an **8px linear scale**. To maintain the "calming" aspect of the brand, we prioritize generous internal padding within cards and containers (the `md` and `lg` units) to prevent the UI from feeling cluttered. Content blocks are separated by significant vertical margins to allow the user to focus on one concept at a time.

## Elevation & Depth

Visual hierarchy is achieved through **Ambient Shadows** and **Tonal Layering**. 

- **Surface 0 (Background):** A very light, cool-tinted gray (#F8FAFC).
- **Surface 1 (Cards/Main UI):** Pure white, using extra-diffused shadows with a 10% opacity blue-tint (e.g., `0 8px 24px rgba(93, 156, 236, 0.1)`).
- **Surface 2 (Interactive/Floating):** Slightly more pronounced shadows to indicate "clickability."

Shadows should never be harsh or black. They should feel like soft glows that lift the content off the page, creating a tactile, "squishy" feel that appeals to the younger demographic.

## Shapes

The shape language is characterized by **Generous Rounding**. There are no sharp corners in the design system.

- **Standard Elements (Buttons, Inputs):** Use a 0.5rem (8px) radius.
- **Containers (Cards, Modals):** Use a 1rem (16px) radius for a friendly, safe appearance.
- **Interactive Pill Components:** Tags and active status indicators use full 100px rounding to differentiate them from static content.

## Components

- **Gamified Buttons:** Buttons use a "thick" bottom border (2-4px) in a slightly darker shade of the primary color to create a 3D "pressable" effect. On hover, the button translates 2px down to simulate a physical click.
- **Interactive Cards:** Cards contain a soft 1px border colored at 50% opacity of the primary blue to provide structure without adding visual noise.
- **Progress Trackers:** Uses the "Positive Orange" for milestones and "Soft Green" for completed modules. The shapes are circular and bubbly.
- **Input Fields:** Large tap targets (minimum 48px height) with soft-colored backgrounds rather than heavy outlines. Focus states use a 2px "Soft Blue" glow.
- **Chips & Tags:** Small, rounded-pill shapes used for categorizing types of bullying or emotional states, utilizing low-saturation versions of the core palette.
- **Avatars:** Always circular, with a subtle 2px "Soft Green" ring to indicate active or "safe" peer mentors.