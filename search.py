import spacy
import sys
#search.py

# Load the English model
nlp = spacy.load("en_core_web_sm")

def search_text(text_file, query):
    with open(text_file, 'r') as file:
        text = file.read()

    # Process the text using spaCy
    doc = nlp(text)

    # Check for presence of query in the text
    found_keywords = [token.text for token in doc if token.text.lower() == query.lower()]

    return found_keywords

# Main function
def main():
    text_file = sys.argv[1]
    query = sys.argv[2]
    found_keywords = search_text(text_file, query)
    print("Found keywords:", found_keywords)

if __name__ == "__main__":
    main()
