# Shop Module CSS Organization

## Overview
The shop module CSS has been refactored into a modular structure with common styles and page-specific styles separated.

## Structure

### `shop-common.css` (Common/Shared Styles)
Contains all common styles used across all shop pages:
- CSS Variables (colors, theme)
- Typography and base styles
- Summary/Stat cards
- Tables and table cells
- Buttons and action buttons
- Badges and status indicators
- Forms and form controls
- Modals and notifications
- Loading animations
- Empty states
- Responsive design base

**Size**: ~4.5KB (minified)

### Page-Specific CSS Files

#### `shop-dashboard.css`
Dashboard page specific styles:
- Dashboard header styles
- Dashboard layout adjustments

**Imports**: `shop-common.css`

#### `shop-products.css`
Product Management page specific styles:
- Product image styles
- Product preview/upload
- Product image placeholders
- Category badges (product-specific)
- Stock level indicators
- Rating styles

**Imports**: `shop-common.css`

#### `shop-facilities.css`
Facility Management page specific styles:
- Facility card layouts
- Facility status indicators
- Facility header styles
- Facility info display
- Facility action buttons

**Imports**: `shop-common.css`

#### `shop-inventory.css`
Inventory Management page specific styles:
- Filter section styles
- Stock quantity indicators with animations
- Inventory status badges
- Table header enhancements
- Search box and filter dropdown
- Action button variations (btn-small with ripple effects)
- Premium animations and indicators

**Imports**: `shop-common.css`

#### `shop-orders.css`
Order Management page specific styles:
- Order-specific component styles
- Responsive adjustments

**Imports**: `shop-common.css`

#### `shop-rentals.css`
Equipment Rentals page specific styles:
- Rental-specific component styles
- Responsive adjustments

**Imports**: `shop-common.css`

## Benefits

1. **Modularity**: Each page has its own CSS file with only the styles it needs
2. **Maintainability**: Easier to find and update page-specific styles
3. **Performance**: Smaller CSS file sizes loaded per page
4. **Reusability**: Common styles are written once and imported everywhere
5. **Scalability**: Easy to add new pages and their specific styles
6. **Consistency**: All pages inherit the same base design system

## Usage

### For Shop Dashboard
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-dashboard.css">
```

### For Products Page
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-products.css">
```

### For Facilities Page
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-facilities.css">
```

### For Inventory Page
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-inventory.css">
```

### For Orders Page
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-orders.css">
```

### For Rentals Page
```html
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-rentals.css">
```

## Migration Notes

- The old `shop.css` now serves as a redirect that imports `shop-common.css`
- Each page-specific file includes `@import url('shop-common.css')` at the top
- No breaking changes - existing pages using `shop.css` will still work
- New pages should use the page-specific CSS file for better organization

## Color Theme

All files use the same color variables defined in `shop-common.css`:
```css
--shop-primary: #4A90E2      /* Primary blue */
--shop-secondary: #357ABD    /* Secondary blue */
--shop-success: #27ae60      /* Success green */
--shop-warning: #f39c12      /* Warning orange */
--shop-danger: #e74c3c       /* Danger red */
--shop-info: #4A90E2         /* Info blue */
```

## Responsive Breakpoints

All files follow the same responsive design approach:
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: 480px - 767px
- **Extra Small**: 0px - 479px

## Adding New Styles

1. Identify if the style is common or page-specific
2. If common: Add to `shop-common.css`
3. If page-specific: Add to the relevant page-specific CSS file
4. Ensure responsive design is included for all screen sizes
5. Use existing color variables and animations
