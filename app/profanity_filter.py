from better_profanity import profanity

profanity.load_censor_words()

def check_profanity(text: str):
    contains = profanity.contains_profanity(text)
    cleaned = profanity.censor(text)

    return {
        "contains_profanity": contains,
        "cleaned_text": cleaned
    }
