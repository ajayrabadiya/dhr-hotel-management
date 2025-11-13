# DHR Hotel Management Plugin

A comprehensive WordPress plugin for managing hotels with Google Maps integration.

## Features

- **Backend Management:**
  - Add, Edit, Delete, and List hotels
  - Full CRUD functionality
  - Admin menu: "DHR Hotel Management"
  - Hotel information management (name, address, coordinates, contact info, etc.)

- **Frontend Display:**
  - Google Maps integration with hotel markers
  - Interactive map with info windows
  - Responsive design matching the provided design
  - Shortcode support: `[dhr_hotel_map]`

## Installation

1. Upload the `dhr-hotel-management` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **DHR Hotel Management** in the admin menu

## Configuration

### Google Maps API Key

1. Get a Google Maps API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable the following APIs:
   - Maps JavaScript API
   - Places API (optional, for future enhancements)
3. Open `includes/class-dhr-hotel-frontend.php`
4. Replace `YOUR_API_KEY` with your actual API key on line 30

## Usage

### Backend

1. Navigate to **DHR Hotel Management > All Hotels** to view all hotels
2. Click **Add New** to add a new hotel
3. Fill in the hotel details:
   - Hotel Name (required)
   - Address (required)
   - City (required)
   - Province (required)
   - Latitude & Longitude (required) - Use Google Maps to find coordinates
   - Contact information (phone, email, website)
   - Image URL or upload image
   - Google Maps URL
4. Click **Add Hotel** or **Update Hotel** to save

### Frontend

Use the shortcode on any page or post:

```
[dhr_hotel_map]
```

**Shortcode Parameters:**
- `province` - Filter hotels by province (e.g., `[dhr_hotel_map province="Western Cape"]`)
- `city` - Filter hotels by city (e.g., `[dhr_hotel_map city="Cape Town"]`)
- `height` - Set map height (e.g., `[dhr_hotel_map height="800px"]`)

**Example:**
```
[dhr_hotel_map province="Western Cape" height="700px"]
```

## Database

The plugin creates a table `wp_dhr_hotels` with the following structure:
- id (primary key)
- name
- description
- address
- city
- province
- country
- latitude
- longitude
- phone
- email
- website
- image_url
- google_maps_url
- status (active/inactive)
- created_at
- updated_at

## Design

The frontend design matches the provided specifications:
- Left panel (33% width) with hotel information
- Right panel (67% width) with Google Maps
- Info windows on map markers
- Responsive design for mobile devices

## Support

For issues or questions, please contact the plugin developer.

## License

GPL v2 or later


