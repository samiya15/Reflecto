from fastapi import FastAPI
from pydantic import BaseModel
from sentiment_analysis import analyze_sentiment
from profanity_filter import check_profanity

app = FastAPI()

class FeedbackInput(BaseModel):
    message: str

@app.get("/")
def read_root():
    return {"message": "Hello from Reflcto API!"}

@app.post("/feedback/analyze")
def analyze_feedback(data: FeedbackInput):
    sentiment_result = analyze_sentiment(data.message)
    profanity_result = check_profanity(data.message)

    return {
        **sentiment_result,
        **profanity_result
    }
