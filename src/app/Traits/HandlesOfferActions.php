<?php

namespace App\Traits;

use App\Models\ProjectOffer;
use App\Models\Message;
use App\Models\ProjectReview;
use Illuminate\Support\Facades\Auth;

trait HandlesOfferActions
{
    public $showQuoteModal = false;
    public $quoteOfferId = null;
    public $quoteMessage = '';

    public $showReviewModal = false;
    public $activeOfferIdForReview = null;
    public $reviewRating = 5;
    public $reviewComment = '';
    public $reviewType = 'validate';
    public $replyTo = null;

    public function setReviewOffer($offerId, $showModal = false)
    {
        if ($this->activeOfferIdForReview === $offerId && !$showModal) {
            $this->activeOfferIdForReview = null;
        } else {
            $this->activeOfferIdForReview = $offerId;
        }
        
        $this->showReviewModal = $showModal;
    }

    public function setQuoteOffer($offerId, $showModal = true)
    {
        $this->quoteOfferId = $offerId;
        $this->showQuoteModal = $showModal;
        
        if ($showModal && $offerId) {
            $offer = ProjectOffer::find($offerId);
            $this->quoteMessage = "Bonjour,\n\nJe souhaiterais obtenir un devis pour l'article : " . ($offer->title ?? 'votre offre') . ".\n\nMerci par avance !";
        }
    }

    public function submitQuoteRequest()
    {
        $this->validate([
            'quoteMessage' => 'required|min:5|max:2000',
            'quoteOfferId' => 'required|exists:project_offers,id',
        ]);

        $offer = ProjectOffer::findOrFail($this->quoteOfferId);
        $project = $offer->project;

        Message::create([
            'project_id' => $project->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $project->owner_id,
            'content' => $this->quoteMessage,
            'type' => 'chat',
            'metadata' => [
                'type' => 'quote_request',
                'offer_id' => $this->quoteOfferId,
                'offer_title' => $offer->title,
            ],
        ]);

        $this->quoteMessage = '';
        $this->quoteOfferId = null;
        $this->showQuoteModal = false;
        session()->flash('success', 'Votre demande de devis a été envoyée en message privé au propriétaire du projet !');
        
        if (method_exists($this, 'refresh')) {
            $this->refresh();
        }
    }

    public function submitReview()
    {
        $this->validate([
            'reviewComment' => 'required|min:5',
            'reviewRating' => 'required|integer|min:1|max:5',
        ]);

        $offer = ProjectOffer::findOrFail($this->activeOfferIdForReview);
        $project = $offer->project;

        // If it's a new review (not a reply)
        if (!$this->replyTo) {
            $exists = ProjectReview::where('project_id', $project->id)
                ->where('user_id', Auth::id())
                ->where('project_offer_id', $this->activeOfferIdForReview)
                ->whereNull('parent_id')
                ->exists();
            
            if ($exists) {
                session()->flash('error', 'Vous avez déjà laissé un avis sur cet article.');
                return;
            }
        } else {
            if (!$project->canManage(Auth::user())) {
                session()->flash('error', 'Seuls les administrateurs du projet peuvent répondre aux avis.');
                return;
            }
        }

        ProjectReview::create([
            'project_id' => $project->id,
            'project_offer_id' => $this->activeOfferIdForReview,
            'user_id' => Auth::id(),
            'type' => $this->reviewType,
            'rating' => $this->reviewRating,
            'comment' => $this->reviewComment,
            'parent_id' => $this->replyTo,
        ]);

        $this->reviewComment = '';
        $this->reviewRating = 5;
        $this->replyTo = null;
        $this->activeOfferIdForReview = null;
        $this->showReviewModal = false;
        session()->flash('success', $this->replyTo ? 'Réponse publiée !' : 'Avis publié !');
        
        if (method_exists($this, 'refresh')) {
            $this->refresh();
        } elseif (method_exists($this->project ?? null, 'refresh')) {
            $this->project->refresh();
        }
    }
}
