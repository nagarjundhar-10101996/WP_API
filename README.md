# WP_API
BASIC CURL OPERATION USING CORE PHP OBJECT ORIENTED
How to use the wordpress_api
1. curl http://localhost/wp_api/v1/page , to print the all tables
2. curl http://localhost/wp_api/v1/page/1 , to print chunks of table as page index
3. curl http://localhost/wp_api/v1/page/1?id=[id] or search?id=16 or any execept ?id=16, to print specifice data for id
Note : [must be name uri]?id=16 for search
4. curl http://localhost/wp_api/v1/page/update --data body={"id":2139,"post_type":'page',"post_title":'Update Post Tilte'}  -H 'Content-Type: application/json'
for update specific data 
I recommend specific REST API CLIENT TO TEST USING VS CODE Thunder client 
 Add HEADER FOR TOKEN TO AUTHERIZED FOR SUCH OPERATION  
