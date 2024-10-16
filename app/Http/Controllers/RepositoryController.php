<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RepositoryController extends Controller
{
    /**
     * @return array<array>
     */
    public function search()
    {
        // Si la requête n'a pas de paramètre ou si le paramètre 'q' n'est pas une chaîne de caractères.
        // ---------------------------------------------------------------------------------------------
        if (!request()->has('q') || !is_string(request('q'))) {

            // Alors on retourne une erreur 422.
            // ---------------------------------
            return response()->json(['error' => "Le paramètre 'q' doit être une chaîne de caractères non vide."], 422);
        }

        // Si la longueur du paramètre 'q' est supérieure à 256 caractères.
        // ----------------------------------------------------------------
        if (strlen(request('q')) > 256) {

            // Alors on retourne une erreur 422.
            // ---------------------------------
            return response()->json(['error' => "Le paramètre 'q' ne doit pas être plus long que 256 caractères."], 422);
        }

        // Si la longueur du paramètre 'q' est égale à 256 caractères.
        // -----------------------------------------------------------
        if (strlen(request('q')) == 256) {

            // Alors on retourne un succès 200.
            // --------------------------------
            return response()->json(['success' => "Le paramètre 'q' est égal à 256 caractères."], 200);
        }

        $httpClient = new Client();

        // On prépare les URLs pour GitHub et GitLab.
        // ------------------------------------------
        $githubUrl = 'https://api.github.com/search/repositories?per_page=5&q=' . request()->get('q');
        $gitlabUrl = 'https://gitlab.com/api/v4/projects?per_page=5&order_by=id&sort=asc&search=' . request()->get('q');

        try {
            // On récupère les repositories depuis GitHub.
            // -------------------------------------------
            $githubResp = $httpClient->get($githubUrl);
            $githubRepositories = @json_decode($githubResp->getBody()->getContents(), true)['items'] ?: [];

            // On récupère les repositories depuis GitLab.
            // -------------------------------------------
            $gitlabResp = $httpClient->get($gitlabUrl);
            $gitlabRepositories = @json_decode($gitlabResp->getBody()->getContents(), true) ?: [];
            
        } catch (GuzzleException $e) {

            // On retourne une erreur 500.
            // ---------------------------
            return response()->json(['error' => "Impossible de se connecter aux API GitHub ou GitLab."], 500);
        }

        $returnGithub = [];

        // Mise en forme dans un tableau les résultats GitHub.
        // ---------------------------------------------------
        foreach ($githubRepositories as $r) {
            $returnGithub[] = [
                'repository' => $r['name'],
                'full_repository_name' => $r['full_name'],
                'description' => $r['description'],
                'creator' => @$r['owner']['username'] ?: $r['owner']['login'],
            ];

        }

        $returnGitlab = [];

        // Mise en forme dans un tableau les résultats GitLab.
        // ---------------------------------------------------
        foreach ($gitlabRepositories as $r) {
            $returnGitlab[] = [
                'repository' => $r['name'],
                'full_repository_name' => $r['path_with_namespace'],
                'description' => $r['description'],
                'creator' => @$r['namespace']['path'],
            ];

        }

        // On fusionne les résultats des deux tableux.
        // -------------------------------------------
        $return = array_merge($returnGitlab, $returnGithub);

        // On retourne le tableau fusionné et un code OK 200.
        // --------------------------------------------------
        return response()->json($return, 200);

    }

}
