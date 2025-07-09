from better_profanity import profanity

# Load built-in words first
profanity.load_censor_words()  

# Load custom words (append to built-in)
def load_combined_words():
    try:
        with open("custom_words.txt", "r") as file:
            custom_words = [word.strip().lower() for word in file if word.strip()]
        profanity.add_censor_words(custom_words)
    except FileNotFoundError:
        pass  # If the file doesn't exist yet, skip it

load_combined_words()

def check_profanity(text: str):
    contains_profanity = profanity.contains_profanity(text)
    cleaned_text = profanity.censor(text)
    return {
        "contains_profanity": contains_profanity,
        "cleaned_text": cleaned_text
    }