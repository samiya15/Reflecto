from fastapi import FastAPI, Form
from pydantic import BaseModel
from fastapi.middleware.cors import CORSMiddleware
from sentiment_analysis import analyze_sentiment
from profanity_filter import check_profanity, load_combined_words

app = FastAPI()

# Load default + custom profanity words at startup
load_combined_words()

@app.post("/admin/add-profanity-word")
def add_profanity_word(word: str = Form(...)):
    word = word.strip().lower()

    # Append to custom list
    with open("custom_words.txt", "a") as file:
        file.write(f"{word}\n")

    # Reload all words (default + custom)
    load_combined_words()

    return {"message": f"'{word}' added to profanity list."}

# Allow CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class FeedbackInput(BaseModel):
    message: str
    is_anonymous: bool

@app.post("/feedback/analyze")
def analyze_feedback(data: FeedbackInput):
    sentiment_result = analyze_sentiment(data.message)
    profanity_result = check_profanity(data.message)
    return {**sentiment_result, **profanity_result, "is_anonymous": data.is_anonymous}