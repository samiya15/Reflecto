from fastapi import FastAPI
from pydantic import BaseModel
from fastapi.middleware.cors import CORSMiddleware
from sentiment_analysis import analyze_sentiment
from profanity_filter import check_profanity

app = FastAPI()

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
