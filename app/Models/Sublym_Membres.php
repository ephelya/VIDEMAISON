<?php
namespace Models;
use \Models\Admin;
use Models\Membres as ModelsMembres;
use Utils\Forms;

class Sublym_Membres extends Membres {
    public  function registerSublymMember() // 
    {
        $avatarparams = array(); //details sur quantités à produire par ex
        $dreamsparams = array(); //details sur quantités à produire par ex
        $sublym_member = $this;
        $sublym_member = $this -> getAvatarDetails();
        $sublym_member = $this -> getDreamsDetails();
    } 
    public function getAvatarDetails() 
    {
        $avatarparams = self::getAvatarParams($this -> idMembre);
        foreach ($avatarparams as $param => $value) { $$param = $value; }
        $sublym_member = $this;
        //on récupère sa photo

        //on récupère, on enregistre le descriptif à partir des critères de la bdd

        //on enregistre les paramètres du membres

        //on génère 4 photos par DALLE
        $nb_dalle;

        //on choisit la meilleure
        $nb_dalle_ch;

        //on génère un prompt MJ avec image + prompt

        //on génère 4 photos par MJ
        $nb_MJ;

        //on choisit la meilleure
        $nb_MJ_ch;

        //on attribue la photo générée au membre
        return $this;
    } 
    public function getDreamsDetails($params) 
    {
        $avatarparams = self::getDreamsParams($this -> idMembre);
        $sublym_member = $this;
        //on récupère le rêve du membre

        //on génère le prompt de la photo du membre accomplissant son rêve

        //on génère nb_dalle images de la scène avec DALL-E
        $nb_dalle;

        //on choisit nb_dalle_ch meilleure
        $nb_dalle_ch;

        //on génère un prompt MJ avec image + prompt

        //on génère nb_MJ photos par MJ
        $nb_MJ;

        //on choisit nb_MJ_ch meilleure
        $nb_MJ_ch;

        //on attribue la photo générée au rêve
        return $this;
    } 

    
    

}