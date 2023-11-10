import requests

# This is a simulation of the POST request you would send in your PHP code.
# Replace with your actual endpoint and POST data structure.

# Endpoint URL
url = "https://scrape-js.onrender.com/scrape"

# POST data to be sent to the endpoint
data = {
    "secret_key": "test",
    "url": "https://www.linkedin.com/company/alpine-laser/",
    "postSelector": 'li[class="mb-1"]',
    "selectorsArray": [
        'li[class="mb-1"] article',
        'time',
        'span[data-test-id="social-actions_reaction-count"]',
        'a[data-id="social-actions_comments"]',
        'a[data-tracking-control-name="organization_guest_main-feed-card_feed-actor-name"]'
    ],
    "attributesArray": [
        "data-activity-urn",
        "innerText",
        "innerText",
        "innerText",
        "innerText"
    ],
    "namesArray": [
        "URN",
        "age",
        "reactions",
        "comments",
        "company-name"
    ]
}

# Make the POST request and get the response
response = requests.post(url, json=data)

# Check if the request was successful
if response.status_code == 200:
    # The request was successful, print the response text
    print(response.text)
else:
    # There was an error, print the status code and response text
    print(f"Error: {response.status_code}")
    print(response.text)


for one post:import requests
from urllib.parse import urlparse

def extract_company_name(url):
    parsed_url = urlparse(url)
    path_parts = parsed_url.path.split('/')
    if 'company' in path_parts:
        company_index = path_parts.index('company') + 1
        if company_index < len(path_parts):
            return path_parts[company_index]
    return None
# Define the URL of the Node.js server
url = "https://scrape-js.onrender.com/scrape"

# Define the data to be sent in the POST request
data = {
    "secret_key": "test",
    "url": "https://www.linkedin.com/posts/alpine-laser_just-completed-the-installation-of-two-femtosecond-activity-7084633761740423169-tC06",
    "postSelector": 'section[class="mb-3"]',
    "selectorsArray": ,
    "attributesArray": ["data-attributed-urn","innerText","src","innerText","innerText","innerText","src"],
    "namesArray": ["URN","age","profilePicture","post_text","reactions" ,"comments","images"]
}

try:
    # Make the POST request
    response = requests.post(url, json=data)

    # Print the response
    res =  response.json()
    print(res)
except requests.exceptions.RequestException as e:
    print("An error occurred during the API call:", str(e))


    import requests
from urllib.parse import urlparse

def extract_company_name(url):
    parsed_url = urlparse(url)
    path_parts = parsed_url.path.split('/')
    if 'company' in path_parts:
        company_index = path_parts.index('company') + 1
        if company_index < len(path_parts):
            return path_parts[company_index]
    return None
# Define the URL of the Node.js server
url = "https://scrape-js.onrender.com/scrape"

# Define the data to be sent in the POST request
data = {
    "secret_key": "test",
    "url": "https://www.linkedin.com/company/alpine-laser/",
    "postSelector": 'li[class="mb-1"]',
    "selectorsArray": ,
    "attributesArray": ["data-activity-urn","innerText","innerText","innerText","innerText"],
    "namesArray": ["URN","company_name","age","reactions" ,"comments"]
}

try:
    # Make the POST request
    response = requests.post(url, json=data)

    # Print the response
    res =  response.json()
    print(res)
    
except requests.exceptions.RequestException as e:
    print("An error occurred during the API call:", str(e))