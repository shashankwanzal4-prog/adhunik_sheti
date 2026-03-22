<?php
/**
 * Courier API Integration Configuration
 * Configure your courier partner APIs for real-time tracking
 */

class CourierAPI {
    private $courier_name;
    private $api_key;
    private $api_secret;
    private $base_url;
    private $mode; // 'test' or 'live'
    
    // Supported Courier Configurations
    private static $courier_configs = [
        'delhivery' => [
            'name' => 'Delhivery',
            'test_url' => 'https://staging-express.delhivery.com',
            'live_url' => 'https://track.delhivery.com',
            'tracking_url' => 'https://www.delhivery.com/track/package/',
            'api_docs' => 'https://docs.delhivery.com/'
        ],
        'shiprocket' => [
            'name' => 'ShipRocket',
            'test_url' => 'https://apiv2.shiprocket.in/v1/external',
            'live_url' => 'https://apiv2.shiprocket.in/v1/external',
            'tracking_url' => 'https://track.shiprocket.co/',
            'api_docs' => 'https://apidocs.shiprocket.in/'
        ],
        'bluedart' => [
            'name' => 'BlueDart',
            'test_url' => 'https://apigateway.bluedart.com',
            'live_url' => 'https://apigateway.bluedart.com',
            'tracking_url' => 'https://www.bluedart.com/track/',
            'api_docs' => 'https://www.bluedart.com/api/'
        ],
        'dtdc' => [
            'name' => 'DTDC',
            'test_url' => 'https://staging-api.dtdc.com',
            'live_url' => 'https://api.dtdc.com',
            'tracking_url' => 'https://www.dtdc.in/trace.asp',
            'api_docs' => 'https://www.dtdc.in/api/'
        ],
        'india_post' => [
            'name' => 'India Post',
            'test_url' => 'https://api.indiapost.gov.in',
            'live_url' => 'https://api.indiapost.gov.in',
            'tracking_url' => 'https://www.indiapost.gov.in/_layouts/15/dop.portal.tracking/trackconsignment.aspx',
            'api_docs' => 'https://api.indiapost.gov.in/'
        ]
    ];
    
    public function __construct($courier_name, $api_key = '', $api_secret = '', $mode = 'test') {
        $this->courier_name = strtolower($courier_name);
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->mode = $mode;
        
        if (isset(self::$courier_configs[$this->courier_name])) {
            $config = self::$courier_configs[$this->courier_name];
            $this->base_url = ($mode === 'live') ? $config['live_url'] : $config['test_url'];
        }
    }
    
    /**
     * Get tracking URL for a shipment
     */
    public function getTrackingUrl($tracking_number) {
        if (isset(self::$courier_configs[$this->courier_name])) {
            $base_url = self::$courier_configs[$this->courier_name]['tracking_url'];
            return $base_url . $tracking_number;
        }
        return 'https://www.google.com/search?q=' . urlencode($this->courier_name . ' tracking ' . $tracking_number);
    }
    
    /**
     * Track shipment (API integration placeholder)
     */
    public function trackShipment($tracking_number) {
        // This is a placeholder for actual API integration
        // Each courier has different API endpoints and authentication
        
        $response = [
            'success' => false,
            'message' => 'API integration required. Please configure your API credentials.',
            'tracking_number' => $tracking_number,
            'courier' => $this->courier_name,
            'status' => null,
            'events' => [],
            'estimated_delivery' => null
        ];
        
        // Example API call structure (to be implemented based on courier docs):
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/track/' . $tracking_number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        $api_response = curl_exec($ch);
        curl_close($ch);
        
        // Parse response based on courier's API format
        $data = json_decode($api_response, true);
        */
        
        return $response;
    }
    
    /**
     * Create shipment (API integration placeholder)
     */
    public function createShipment($order_data) {
        // Placeholder for creating shipments via API
        return [
            'success' => false,
            'message' => 'API integration required. Please configure your API credentials.',
            'shipment_id' => null,
            'tracking_number' => null,
            'label_url' => null
        ];
    }
    
    /**
     * Cancel shipment (API integration placeholder)
     */
    public function cancelShipment($tracking_number) {
        // Placeholder for cancelling shipments
        return [
            'success' => false,
            'message' => 'API integration required. Please configure your API credentials.'
        ];
    }
    
    /**
     * Get available couriers
     */
    public static function getAvailableCouriers() {
        return self::$courier_configs;
    }
    
    /**
     * Get courier API documentation URL
     */
    public function getApiDocs() {
        return self::$courier_configs[$this->courier_name]['api_docs'] ?? '#';
    }
}

/**
 * How to use:
 * 
 * 1. Sign up with a courier partner (Delhivery, ShipRocket, etc.)
 * 2. Get API credentials from their dashboard
 * 3. Configure below:
 */

$courier_config = [
    'default_courier' => 'delhivery', // Change to your preferred courier
    
    'delhivery' => [
        'enabled' => false, // Set to true after configuring API
        'api_key' => 'YOUR_DELHIVERY_API_KEY',
        'api_secret' => 'YOUR_DELHIVERY_API_SECRET',
        'mode' => 'test', // 'test' or 'live'
        'pickup_location' => [
            'name' => 'Adhunik Krushi Bhandar',
            'address' => 'Your Warehouse Address',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'pincode' => '411001',
            'phone' => '+91-9588676848'
        ]
    ],
    
    'shiprocket' => [
        'enabled' => false,
        'api_key' => 'YOUR_SHIPROCKET_API_KEY',
        'api_secret' => 'YOUR_SHIPROCKET_API_SECRET',
        'email' => 'your-email@example.com',
        'password' => 'your-password',
        'mode' => 'test'
    ],
    
    'bluedart' => [
        'enabled' => false,
        'api_key' => 'YOUR_BLUEDART_API_KEY',
        'api_secret' => 'YOUR_BLUEDART_API_SECRET',
        'mode' => 'test'
    ]
];

/**
 * Setup Instructions:
 * 
 * 1. DELHIVERY:
 *    - Register at: https://www.delhivery.com/
 *    - Get API keys from dashboard
 *    - Complete KYC verification
 *    - Configure pickup locations
 * 
 * 2. SHIPROCKET:
 *    - Register at: https://www.shiprocket.in/
 *    - Connect your store
 *    - Add API credentials
 *    - Set up courier preferences
 * 
 * 3. BLUE DART:
 *    - Contact BlueDart for API access
 *    - Complete business verification
 *    - Get API credentials
 * 
 * 4. DTDC:
 *    - Register at: https://www.dtdc.in/
 *    - Apply for API access
 *    - Configure integration
 * 
 * Once configured, set 'enabled' => true for your chosen courier
 * and the system will automatically sync tracking information.
 */

// Example usage:
// $courier = new CourierAPI('delhivery', $courier_config['delhivery']['api_key']);
// $tracking_info = $courier->trackShipment('TRACKING_NUMBER');
