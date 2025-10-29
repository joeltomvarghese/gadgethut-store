<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("No file uploaded or upload error");
        }

        $uploadedFile = $_FILES['image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($uploadedFile['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only JPG, PNG, GIF, and WebP images are allowed");
        }
        
        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($uploadedFile['size'] > $maxSize) {
            throw new Exception("File size too large. Maximum size is 5MB");
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = '../uploads/products/' . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
            // Return success with file URL
            $base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
            $imageUrl = $base_url . "/../uploads/products/" . $fileName;
            
            echo json_encode([
                "success" => true,
                "message" => "File uploaded successfully",
                "fileUrl" => $imageUrl,
                "fileName" => $fileName
            ]);
        } else {
            throw new Exception("Failed to move uploaded file");
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>