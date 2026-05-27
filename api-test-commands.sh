#!/bin/bash

echo "========================================="
echo "API Testing Commands"
echo "========================================="

# Base URL
BASE_URL="http://localhost:8000/api"

echo ""
echo "1. REGISTER a new user:"
echo "curl -X POST $BASE_URL/register \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -d '{\"name\":\"Test User\",\"email\":\"test@test.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}'"
echo ""

echo "2. LOGIN:"
echo "curl -X POST $BASE_URL/login \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -d '{\"email\":\"admin1@blog.com\",\"password\":\"password\"}'"
echo ""

echo "3. GET ALL POSTS (public):"
echo "curl -X GET $BASE_URL/posts"
echo ""

echo "4. GET SINGLE POST:"
echo "curl -X GET $BASE_URL/posts/1"
echo ""

echo "5. CREATE POST (authenticated):"
echo "curl -X POST $BASE_URL/posts \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -H \"Authorization: Bearer YOUR_TOKEN\" \\"
echo "  -d '{\"title\":\"API Post\",\"body\":\"This is a very long body content for the API post that meets the minimum length requirement of 100 characters. This should be enough.\",\"status\":\"published\",\"category_ids\":[1,2]}'"
echo ""

echo "6. UPDATE POST:"
echo "curl -X PUT $BASE_URL/posts/1 \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -H \"Authorization: Bearer YOUR_TOKEN\" \\"
echo "  -d '{\"title\":\"Updated API Post\"}'"
echo ""

echo "7. DELETE POST:"
echo "curl -X DELETE $BASE_URL/posts/1 \\"
echo "  -H \"Authorization: Bearer YOUR_TOKEN\""
echo ""

echo "8. LOGOUT:"
echo "curl -X POST $BASE_URL/logout \\"
echo "  -H \"Authorization: Bearer YOUR_TOKEN\""
echo ""
