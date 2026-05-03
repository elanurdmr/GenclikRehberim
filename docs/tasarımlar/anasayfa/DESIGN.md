---
name: Gençlik Rehberim Design System
colors:
  surface: '#f8f9ff'
  surface-dim: '#d8dae1'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f3fb'
  surface-container: '#ecedf5'
  surface-container-high: '#e6e8ef'
  surface-container-highest: '#e1e2e9'
  on-surface: '#191c21'
  on-surface-variant: '#414751'
  inverse-surface: '#2e3036'
  inverse-on-surface: '#eff0f8'
  outline: '#717783'
  outline-variant: '#c1c7d3'
  surface-tint: '#0060ac'
  primary: '#005da7'
  on-primary: '#ffffff'
  primary-container: '#2976c7'
  on-primary-container: '#fdfcff'
  inverse-primary: '#a4c9ff'
  secondary: '#3a6a00'
  on-secondary: '#ffffff'
  secondary-container: '#a1fa49'
  on-secondary-container: '#3e7100'
  tertiary: '#686000'
  on-tertiary: '#ffffff'
  tertiary-container: '#bbae00'
  on-tertiary-container: '#464100'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d4e3ff'
  primary-fixed-dim: '#a4c9ff'
  on-primary-fixed: '#001c39'
  on-primary-fixed-variant: '#004883'
  secondary-fixed: '#a1fa49'
  secondary-fixed-dim: '#87dc2c'
  on-secondary-fixed: '#0e2000'
  on-secondary-fixed-variant: '#2a5000'
  tertiary-fixed: '#f7e61a'
  tertiary-fixed-dim: '#d9c900'
  on-tertiary-fixed: '#1f1c00'
  on-tertiary-fixed-variant: '#4e4800'
  background: '#f8f9ff'
  on-background: '#191c21'
  surface-variant: '#e1e2e9'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 48px
    fontWeight: '800'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.3'
  title-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: '1.4'
  body-base:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '500'
    lineHeight: '1.6'
  label-caps:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '700'
    lineHeight: '1.0'
    letterSpacing: 0.05em
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
  gutter: 20px
  margin: 24px
---

## Brand & Style
The design system is built on the pillars of "Academic Optimism" and "Vibrant Mentorship." It is specifically tailored for students, aiming to transform the often-stressful educational journey into an engaging, energetic, and supportive experience. 

The visual style blends **Modern/Corporate** reliability with **Tactile** friendliness. By utilizing high-saturation colors alongside soft, organic shapes, the design system establishes a safe space that feels both professional and approachable. Every interaction should feel like a "pat on the back"—motivating, clear, and full of forward momentum.

## Colors
The palette is a celebration of growth and clarity.
- **Sky Blue (#4A90E2):** Used for primary actions and navigation to instill a sense of trust and limitless potential.
- **Lime Green (#7ED321):** Represents progress and success; used for positive feedback, completion states, and "growth" indicators.
- **Sunshine Yellow (#F8E71C):** An attention-grabber for highlights and motivational tips, used sparingly to prevent visual fatigue.
- **Coral Orange (#F5A623):** The "energy" color, used for high-priority calls to action and urgent reminders.
- **Backgrounds:** Use a soft off-white (#F8FAFC) to ensure the vibrant colors pop without straining the eyes.

## Typography
This design system utilizes **Plus Jakarta Sans** for its friendly, modern, and highly legible geometric terminals. 
- **Headlines:** Use Bold or ExtraBold weights with slight negative letter-spacing to create a punchy, confident look.
- **Body Text:** Use Medium weight (500) rather than Regular to maintain a "friendly" thickness that is easy to read against colorful backgrounds.
- **Hierarchy:** Ensure a clear distinction between informational body text and motivational headers to guide the student’s focus effectively.

## Layout & Spacing
The layout follows a **fluid grid** model with generous white space to reduce cognitive load. 
- Use an 8px base rhythm for all internal component spacing.
- On mobile, maintain 24px side margins to give content "breathing room."
- Layouts should prioritize vertical stacking for mobile-first students, using large 40px+ gaps between major sections to prevent a cluttered feeling.

## Elevation & Depth
Depth is created through **Ambient Shadows** that are color-tinted rather than neutral gray. 
- Shadows should have a large blur radius (20px to 40px) and low opacity (10-15%).
- Use a slight blue-ish tint (#4A90E2 at 10% opacity) for shadows on primary elements to make them feel integrated with the brand.
- Interactive elements should "lift" (shadow becomes larger and softer) when hovered or pressed, mimicking physical responsiveness.

## Shapes
The design system embraces a **highly rounded** aesthetic to appear soft and non-threatening.
- **Primary Containers/Cards:** 16px minimum radius.
- **Buttons:** Fully pill-shaped or 12px+ to maintain a friendly, touchable appearance.
- **Decorative Elements:** Use "squiggles" or "blobs" as background accents to reinforce the playful, youthful energy of the brand.

## Components
- **Buttons:** Main buttons use the Primary Sky Blue with white text. "Success" actions use Lime Green. Buttons should have a subtle 2px bottom border (slightly darker than the button color) to give them a tactile, pressable feel.
- **Cards:** White backgrounds with 16px rounded corners and a very soft Sky Blue ambient shadow. Use top-border accents (4px thick) in Coral or Yellow to categorize different types of content (e.g., Orange for "Urgent Task," Yellow for "Tip of the Day").
- **Chips:** Highly rounded (pill) with light background tints (e.g., 10% opacity of the brand color) and dark text.
- **Input Fields:** Thick 2px borders in light gray that turn Primary Blue on focus. Use 12px padding and 12px corner radius.
- **Progress Bars:** Use Lime Green for the fill color on a light gray track to emphasize achievement and growth.
- **Gamification Badges:** Floating circular elements with the energetic Coral color to celebrate student milestones.