<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Product;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionAnswer;
use App\Mail\ProductSuggestions;
use Illuminate\Support\Facades\Mail;

class QuestionAnswerController extends Controller
{


public function selectGender(){
return view('main');
}

public function selectGenderPost(Request $request){
$gender = $request->input('gender');
session(['gender'=> $gender]);

//dd($gender);

return redirect()->route('quiz');
}

    public function index(){
        // Retrieve the gender from the session
    $gender = session('gender');
    
    //dd($gender);

    // Fetch questions based on the gender or all questions if gender is not set
    $questions = $gender ? Question::where('questions_for', $gender)->get() : Question::where('questions_for', 'other')->get();
    
    //dd($questions);


        return view('front', compact('questions'));
    }

   public function store(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'email' => 'required|email',
            'answers' => 'required|array',
            'gender' => 'required',
        ]);
    
        // Store email and gender in session
        session(['user_email' => $validatedData['email']]);
        session(['user_gender' => $validatedData['gender']]);
        
    
        // Store data in the database
        $questionAnswer = QuestionAnswer::create([
            'email' => $validatedData['email'],
            'gender' => $validatedData['gender'],
            'answers' => json_encode($validatedData['answers']),
        ]);
    
        // Decode the user's answers
        $userAnswers = json_decode($questionAnswer->answers, true);
    
        // Fetch products relevant to the user's gender
        $products = Product::where('products_for', $validatedData['gender'])->get();
    
        if ($products->isEmpty()) {
            return redirect()->back()->with('success', 'No products found for suggestions.');
        }
    
        $suggestedProducts = [];
        $similarityScores = [];
    
        foreach ($products as $product) {
            // Decode product's questions
            $productQuestions = is_string($product->question_answers)
                        ? json_decode($product->question_answers, true)
                        : $product->question_answers;
    
            if (!is_array($productQuestions) || is_null($productQuestions)) {
                continue;
            }
    
            $productQuestions = $productQuestions[0];
            $matches = 0;
    
            // Calculate similarity
            foreach ($productQuestions as $key => $value) {
                $index = str_replace('question_', '', $key);
    
                if (isset($userAnswers[$index]) && trim(strtolower($userAnswers[$index])) === trim(strtolower($value))) {
                    $matches++;
                }
            }
    
            $totalQuestions = count($productQuestions);
            $similarityPercentage = ($totalQuestions > 0) ? ($matches / $totalQuestions) * 100 : 0;
    
            $similarityScores[$product->id] = $similarityPercentage;
    
            if ($similarityPercentage === 100) {
                $suggestedProducts[] = $product;
            }
        }
    
        // If no 100% matches, find the most similar products
        if (empty($suggestedProducts) && !empty($similarityScores)) {
            arsort($similarityScores);
            $topSimilarProducts = array_slice($similarityScores, 0, 3, true);
    
            foreach ($topSimilarProducts as $productId => $score) {
                $product = Product::find($productId);
                if ($product) {
                    $suggestedProducts[] = $product;
                }
            }
        } else {
            $suggestedProducts = array_slice($suggestedProducts, 0, 3);
        }
    
        if (!empty($suggestedProducts)) {
            Mail::to($validatedData['email'])->send(new ProductSuggestions($suggestedProducts));
        }
    
        return redirect()->route('suggested.products')->with('success', 'Your answers have been submitted, and suggestions have been sent via email!');
    }



public function suggestedProducts(Request $request)
{
    // Retrieve the user's email and gender from the session
    $email = $request->session()->get('user_email');
    $gender = $request->session()->get('gender'); // Assumes gender is stored in the session

    // Get the question answer record for that email
    $questionAnswer = QuestionAnswer::where('email', $email)->first();

    // If no record found, redirect with an error message
    if (!$questionAnswer) {
        return redirect()->back()->with('error', 'No answers found for this user.');
    }

    // Decode the user's answers
    $userAnswers = json_decode($questionAnswer->answers, true);

    // Fetch all products relevant to the user's gender
    $products = Product::where('products_for', $gender)->get();

    // Initialize an array for suggested products and similarity scores
    $suggestedProducts = [];
    $similarityScores = [];

    foreach ($products as $product) {
        // Decode product's questions
        $productQuestions = is_string($product->question_answers)
                    ? json_decode($product->question_answers, true)
                    : $product->question_answers;

        // Check if productQuestions is an array
        if (!is_array($productQuestions) || is_null($productQuestions)) {
            continue; // Skip if questions are not properly formatted
        }

        // Flatten the product questions (assuming only one set of questions)
        $productQuestions = $productQuestions[0]; // Get the first (and assumed only) associative array

        // Initialize matches counter
        $matches = 0;

        // Calculate similarity
        foreach ($productQuestions as $key => $value) {
            // Extract index from the question key
            $index = str_replace('question_', '', $key);

            // Check if userAnswers has the corresponding index
            if (isset($userAnswers[$index]) && trim(strtolower($userAnswers[$index])) === trim(strtolower($value))) {
                $matches++;
            }
        }

        // Calculate percentage similarity
        $totalQuestions = count($productQuestions);
        $similarityPercentage = ($totalQuestions > 0) ? ($matches / $totalQuestions) * 100 : 0;

        // Store similarity scores
        $similarityScores[$product->id] = $similarityPercentage;

        // If 100% match, add to suggested products immediately
        if ($similarityPercentage === 100) {
            $suggestedProducts[] = $product;
        }
    }

    // If no 100% matches, find the most similar products
    if (empty($suggestedProducts) && !empty($similarityScores)) {
        // Sort products by similarity scores in descending order
        arsort($similarityScores);

        // Get the top 3 most similar products
        $topSimilarProducts = array_slice($similarityScores, 0, 3, true);

        // Fetch these products
        foreach ($topSimilarProducts as $productId => $score) {
            $product = Product::find($productId);
            if ($product) {
                $suggestedProducts[] = $product;
            }
        }
    } else {
        // Limit to top 3 if more than 3 products were found with 100% similarity
        $suggestedProducts = array_slice($suggestedProducts, 0, 3);
    }

    // Pass the suggested products to the view
    return view('suggested-products', compact('suggestedProducts'));
}


}
