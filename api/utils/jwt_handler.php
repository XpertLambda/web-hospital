<?php
class JWTHandler {
    private $secret_key;
    private $issuer;
    private $audience;
    
    public function __construct() {
        // In a real application, you would get these from a config file
        $this->secret_key = "your_secret_key_here";
        $this->issuer = "medical_center_api";
        $this->audience = "medical_center_app";
    }
    
    // Generate JWT token
    public function generateToken($user_id, $role_id, $username, $expire_in = 3600) {
        $issued_at = time();
        $expiration = $issued_at + $expire_in;
        
        $payload = array(
            "iss" => $this->issuer,
            "aud" => $this->audience,
            "iat" => $issued_at,
            "exp" => $expiration,
            "data" => array(
                "id" => $user_id,
                "role_id" => $role_id,
                "username" => $username
            )
        );
        
        return $this->encode($payload);
    }
    
    // Validate token
    public function validateToken($token) {
        try {
            $decoded = $this->decode($token);
            
            // Check if token is expired
            if ($decoded->exp < time()) {
                return false;
            }
            
            return $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Encode data to JWT
    private function encode($payload) {
        // Create JWT header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        // Encode Header
        $base64UrlHeader = $this->base64UrlEncode($header);
        
        // Encode Payload
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        
        // Create Signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        // Create JWT
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    // Decode JWT
    private function decode($jwt) {
        // Split JWT
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
        
        // Get payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded));
        
        // Verify signature
        $signature = $this->base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->secret_key, true);
        
        if ($signature !== $expectedSignature) {
            throw new Exception('Invalid signature');
        }
        
        return $payload;
    }
    
    // Encode to base64Url
    private function base64UrlEncode($data) {
        $b64 = base64_encode($data);
        $url = strtr($b64, '+/', '-_');
        return rtrim($url, '=');
    }
    
    // Decode from base64Url
    private function base64UrlDecode($data) {
        $b64 = strtr($data, '-_', '+/');
        $padding = strlen($b64) % 4;
        if ($padding > 0) {
            $b64 .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($b64);
    }
}
?>