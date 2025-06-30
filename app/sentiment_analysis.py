from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

analyzer = SentimentIntensityAnalyzer()

def analyze_sentiment(text: str):
    score = analyzer.polarity_scores(text)['compound']
    
    if score >= 0.05:
        sentiment = "positive"
    elif score <= -0.05:
        sentiment = "negative"
    else:
        sentiment = "neutral"

    return {
        "sentiment": sentiment,
        "confidence_score": round(score, 2)
    }
