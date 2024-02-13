import spacy

# Load the English model
nlp = spacy.load("en_core_web_sm")

def search_text(text, keywords):
    # Process the text using spaCy
    doc = nlp(text)

    # Check for presence of keywords in the text
    found_keywords = [token.text for token in doc if token.text.lower() in keywords]

    return found_keywords

# Example usage
text = "This is an example text to search for keywords like 'search' and 'text' in."
keywords = ['search', 'text']
found_keywords = search_text(text, keywords)
print("Found keywords:", found_keywords)
