<?phpnamespace Carnaval\WebsiteBundle\Controller;use Carnaval\WebsiteBundle\Entity\CatagorieLiens;use Carnaval\WebsiteBundle\Entity\CategorieVideo;use Carnaval\WebsiteBundle\Entity\Liens;use Carnaval\WebsiteBundle\Entity\Message;use Carnaval\WebsiteBundle\Entity\Evenement;use Carnaval\WebsiteBundle\Entity\Video;use Carnaval\WebsiteBundle\Form\CategorieType;use Carnaval\WebsiteBundle\Form\CategorieVideoType;use Carnaval\WebsiteBundle\Form\EvenementType;use Carnaval\WebsiteBundle\Form\LienType;use Carnaval\WebsiteBundle\Form\MessageType;use Carnaval\WebsiteBundle\Form\VideoType;use Symfony\Bundle\FrameworkBundle\Controller\Controller;use JMS\SecurityExtraBundle\Annotation\Secure;class WebsiteController extends Controller{    /* ===================================== PUBLIC ===================================== */    public function indexAction()    {        /*$user = new User;        $encoder = $this->get('security.encoder_factory')->getEncoder($user);        $encodedPass = $encoder->encodePassword("A1d2m3i4n5istrator", $user->getSalt());        // Le nom d'utilisateur et le mot de passe sont identiques        $user->setUsername("Doudou");        $user->setPassword($encodedPass);        $user->setEmail("douchet.pantoine@gmail.com");        $user->setLastLogin(new \Datetime());        // Le sel et les rôles sont vides pour l'instant        $user->setRoles(array('ROLE_SUPER_ADMIN'));        // On récupère l'EntityManager        $em = $this->getDoctrine()->getManager();        // Étape 1 : On « persiste » l'entité        $em->persist($user);        // Étape 2 : On « flush » tout ce qui a été persisté avant        $em->flush();*/        return $this->render('CarnavalWebsiteBundle:Website:index.html.twig', array());    }	public function presentationAction()	{        return $this->render('CarnavalWebsiteBundle:Website:presentation.html.twig', array());	}	public function referencesAction()	{        return $this->render('CarnavalWebsiteBundle:Website:references.html.twig', array());    }	public function catalogueAction()	{        return $this->render('CarnavalWebsiteBundle:Website:catalogue.html.twig', array());	}	public function calendrierAction()	{        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Evenement');        $newListEvents = array();        $listEvents = $repository->getAllEventsFromDate(new \DateTime());        if(count($listEvents) != 0){            $firstEventDate = clone $listEvents[0]->getEventDate();            $lastEventDate = clone end($listEvents)->getEventDate();            $startDate = $firstEventDate->modify('first day of this month');            $endDate = $lastEventDate->modify('last day of this month')->modify('+1 day');            while(true){                $dateString = $startDate->format('Y-m-d');                $newListEvents[$dateString] = array();                $startDate->modify('+1 month');                if($startDate == $endDate){                    break;                }            }            foreach($newListEvents as $key => $value){                $date = new \DateTime($key);                $nextDate = clone $date;                $nextDate->modify('+1 month');                foreach($listEvents as $event){                    if($event->getEventDate() < $nextDate && $event->getEventDate() > $date){                        $newListEvents[$key][] = $event;                    }                }                $date->modify('+1 month');            }        }        return $this->render('CarnavalWebsiteBundle:Website:calendrier.html.twig', array('events' => $newListEvents));	}	public function galeriesAction()	{        return $this->render('CarnavalWebsiteBundle:Website:galeries.html.twig', array());	}	public function voirGaleriesAction($id)	{        return $this->render('CarnavalWebsiteBundle:Website:voirGalerie.html.twig', array());	}	public function videosAction()	{        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CategorieVideo');        $listeCategories = $repository->getAllActiveCategorie();        foreach($listeCategories as $categorie){            $repository = $this->getDoctrine()                ->getManager()                ->getRepository('CarnavalWebsiteBundle:Video');            $listeLiens = $repository->getAllActiveVideos(array('categorie' => $categorie));            $categorie->setVideos($listeLiens);        }        return $this->render('CarnavalWebsiteBundle:Website:videos.html.twig', array('categories' => $listeCategories));	}	public function voirVideoAction($id)	{        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Video');        $video = $repository->find($id);        /*$str = $video->getDescription();        // On récupère le service        $autoLinker = $this->container->get('dc_autolinker');        $video->setDescription($autoLinker->auto_link_text($str));*/        $urlVideo = $video->getCodeExterne();        $video->setMiniature("http://i.ytimg.com/vi/".$urlVideo."/mqdefault.jpg");        return $this->render('CarnavalWebsiteBundle:Website:voirVideo.html.twig', array('video' => $video));	}	public function contactsAction()	{        $message = new Message();        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new MessageType(), $message);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_contacts'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                if(!trim($message->getSecurity())===''){                    return $this->redirect($this->generateUrl('carnaval_website_contacts'));                }                $message->setReceiveDate(new \DateTime());                $message->setIsDeleted(false);                $message->setIsArchived(false);                $message->setIsRead(false);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($message);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Message envoyé');                $message = new Message();                // On crée le formulaire grâce à l'ArticleType                $form = $this->createForm(new MessageType(), $message);                // On redirige vers la page de visualisation de l'article nouvellement créé              return $this->render('CarnavalWebsiteBundle:Website:contacts.html.twig', array(                    'form' => $form->createView(),                    'formName' => "AJOUTMESSAGE"                ));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:contacts.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTMESSAGE"        ));	}	public function liensAction()	{        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CatagorieLiens');        $listeCategories = $repository->getAllActiveCategorie();        foreach($listeCategories as $categorie){            $repository = $this->getDoctrine()                ->getManager()                ->getRepository('CarnavalWebsiteBundle:Liens');            $listeLiens = $repository->getAllActiveLinks(array('categorie' => $categorie));            $categorie->setLiens($listeLiens);        }        return $this->render('CarnavalWebsiteBundle:Website:liens.html.twig', array('categories' => $listeCategories));	}    /* ===================================== MESSAGES ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $listMessages = $repository->getAllMessages();        return $this->render('CarnavalWebsiteBundle:Website:adm_messages.html.twig', array('messages' => $listMessages));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesArchivedAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $listMessages = $repository->getAllMessagesArchived();        return $this->render('CarnavalWebsiteBundle:Website:adm_messages_archived.html.twig', array('messages' => $listMessages));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesGarbageAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $listMessages = $repository->getAllMessagesDeleted();        return $this->render('CarnavalWebsiteBundle:Website:adm_messages_garbage.html.twig', array('messages' => $listMessages));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesReadAction($id, $return){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $message = $repository->find($id);        $message->setIsRead(true);        $em = $this->getDoctrine()->getManager();        $em->persist($message);        $em->flush();        $returnUrl = '';        if($return == 'read')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages');        elseif($return == 'archived')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages_archived');        elseif($return == 'garbage')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages_garbage');        return $this->render('CarnavalWebsiteBundle:Website:adm_messages_read.html.twig', array('message' => $message, 'returnUrl' => $returnUrl));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesArchiveAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $message = $repository->find($id);        $message->setIsArchived(true);        $em = $this->getDoctrine()->getManager();        $em->persist($message);        $em->flush();        return $this->redirect($this->generateUrl('carnaval_website_adm_messages'));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesDeleteAction($id, $return){        $returnUrl = '';        if($return == 'read')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages');        elseif($return == 'archived')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages_archived');        elseif($return == 'garbage')            $returnUrl = $this->generateUrl('carnaval_website_adm_messages_garbage');        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $message = $repository->find($id);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Annuler' )            return $this->redirect($returnUrl);        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            $message->setIsDeleted(true);            $em = $this->getDoctrine()->getManager();            $em->persist($message);            $em->flush();            // On définit un message flash            $this->get('session')->getFlashBag()->add('info', 'Message supprimé');            return $this->redirect($returnUrl);        }        return $this->render('CarnavalWebsiteBundle:Website:adm_messages_delete.html.twig', array('message' => $message));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admMessagesRealDeleteAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Message');        $message = $repository->find($id);        $message->setIsArchived(true);        $em = $this->getDoctrine()->getManager();        $em->remove($message);        $em->flush();        return $this->redirect($this->generateUrl('carnaval_website_adm_messages_garbage'));    }    /* ===================================== IMAGES ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admImagesAction(){        return $this->render('CarnavalWebsiteBundle:Website:adm_images.html.twig', array());    }    /* ===================================== VIDEOS ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admVideosAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CategorieVideo');        $listeCategories = $repository->getAllActiveCategorie();        foreach($listeCategories as $categorie){            $repository = $this->getDoctrine()                ->getManager()                ->getRepository('CarnavalWebsiteBundle:Video');            $listeVideos = $repository->getAllActiveVideos(array('categorie' => $categorie));            $categorie->setVideos($listeVideos);        }        return $this->render('CarnavalWebsiteBundle:Website:adm_videos.html.twig', array('categories' => $listeCategories));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admVideosAjoutAction(){        $video = new Video;        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new VideoType(), $video);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                $video->setIsActive(true);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($video);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Video ajoutée');                // On redirige vers la page de visualisation de l'article nouvellement créé                return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:adm_videos_ajout.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTVID"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admVideosModifierAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Video');        $video = $repository->find($id);        // On utiliser le ArticleEditType        $form = $this->createForm(new VideoType(), $video);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));        if ($request->getMethod() == 'POST') {            $form->bind($request);            if ($form->isValid()) {                // On enregistre l'article                $em = $this->getDoctrine()->getManager();                $em->persist($video);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Vidéo modifiée');                return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));            }        }        return $this->render('CarnavalWebsiteBundle:Website:adm_videos_modifier.html.twig', array(            'form'    => $form->createView(),            'video' => $video,            'formName' => "MODIFVIDEO"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admVideosSupprimerAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Video');        $video = $repository->find($id);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));        if ($request->getMethod() == 'POST') {            $video->setIsActive(false);            // On enregistre l'article            $em = $this->getDoctrine()->getManager();            $em->persist($video);            $em->flush();            // On définit un message flash            $this->get('session')->getFlashBag()->add('info', 'Vidéo supprimée');            return $this->redirect($this->generateUrl('carnaval_website_adm_videos'));        }        return $this->render('CarnavalWebsiteBundle:Website:adm_videos_supprimer.html.twig', array(            'video' => $video,            'formName' => "SUPPRIMVIDEO"        ));    }    /* ===================================== CATEGORIES VIDEOS ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieVideosAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CategorieVideo');        $listeCategories = $repository->getAllActiveCategorie();        return $this->render('CarnavalWebsiteBundle:Website:adm_categories_videos.html.twig', array('categories' => $listeCategories));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieVideosAjoutAction(){        $categorie = new CategorieVideo();        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new CategorieVideoType(), $categorie);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                $categorie->setIsActive(true);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($categorie);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Categorie Enregistrée');                $categorie = new CategorieVideo();                // On crée le formulaire grâce à l'ArticleType                $form = $this->createForm(new CategorieVideoType(), $categorie);                // On redirige vers la page de visualisation de l'article nouvellement créé                return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:adm_categories_videos_ajout.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTCATEGORIE"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieVideosModifierAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CategorieVideo');        $categorie = $repository->find($id);        // On utiliser le ArticleEditType        $form = $this->createForm(new CategorieVideoType(), $categorie);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));        if ($request->getMethod() == 'POST') {            $form->bind($request);            if ($form->isValid()) {                // On enregistre l'article                $em = $this->getDoctrine()->getManager();                $em->persist($categorie);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Categorie modifiée');                return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));            }        }        return $this->render('CarnavalWebsiteBundle:Website:adm_categories_videos_modifier.html.twig', array(            'form'    => $form->createView(),            'categorie' => $categorie,            'formName' => "MODIFCATEGORIE"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieVideosSupprimerAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CategorieVideo');        $categorie = $repository->find($id);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));        if ($request->getMethod() == 'POST') {            $categorie->setIsActive(false);            // On enregistre l'article            $em = $this->getDoctrine()->getManager();            $em->persist($categorie);            $em->flush();            // On définit un message flash            $this->get('session')->getFlashBag()->add('info', 'Categorie supprimée');            return $this->redirect($this->generateUrl('carnaval_website_adm_categories_videos'));        }        return $this->render('CarnavalWebsiteBundle:Website:adm_categories_videos_supprimer.html.twig', array(            'categorie' => $categorie,            'formName' => "SUPPRIMCATEGORIE"        ));    }    /* ===================================== LIENS ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admLiensAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CatagorieLiens');        $listeCategories = $repository->getAllActiveCategorie();        foreach($listeCategories as $categorie){            $repository = $this->getDoctrine()                ->getManager()                ->getRepository('CarnavalWebsiteBundle:Liens');            $listeLiens = $repository->getAllActiveLinks(array('categorie' => $categorie));            $categorie->setLiens($listeLiens);        }        return $this->render('CarnavalWebsiteBundle:Website:adm_liens.html.twig', array('categories' => $listeCategories));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admLienAjoutAction(){        $lien = new Liens();        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new LienType(), $lien);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                $lien->setIsActive(true);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($lien);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Lien Enregistré');                $lien = new Liens();                // On crée le formulaire grâce à l'ArticleType                $form = $this->createForm(new LienType(), $lien);                // On redirige vers la page de visualisation de l'article nouvellement créé                return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:adm_lien_ajout.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTLIEN"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admLienModifierAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Liens');        $link = $repository->find($id);        // On utiliser le ArticleEditType        $form = $this->createForm(new LienType(), $link);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));        if ($request->getMethod() == 'POST') {            $form->bind($request);            if ($form->isValid()) {                // On enregistre l'article                $em = $this->getDoctrine()->getManager();                $em->persist($link);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Lien modifié');                return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));            }        }        return $this->render('CarnavalWebsiteBundle:Website:adm_lien_modifier.html.twig', array(            'form'    => $form->createView(),            'link' => $link,            'formName' => "MODIFLIEN"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admLienSupprimerAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Liens');        $link = $repository->find($id);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));        if ($request->getMethod() == 'POST') {            $link->setIsActive(false);            // On enregistre l'article            $em = $this->getDoctrine()->getManager();            $em->persist($link);            $em->flush();            // On définit un message flash            $this->get('session')->getFlashBag()->add('info', 'Lien supprimé');            return $this->redirect($this->generateUrl('carnaval_website_adm_liens'));        }        return $this->render('CarnavalWebsiteBundle:Website:adm_lien_supprimer.html.twig', array(            'link' => $link,            'formName' => "SUPPLIEN"        ));    }    /* ===================================== CATEGORIES LIENS ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategoriesAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CatagorieLiens');        $listeCategories = $repository->getAllActiveCategorie();        return $this->render('CarnavalWebsiteBundle:Website:adm_categories.html.twig', array('categories' => $listeCategories));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieAjoutAction(){        $categorie = new CatagorieLiens();        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new CategorieType(), $categorie);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_categories'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                $categorie->setIsActive(true);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($categorie);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Categorie Enregistrée');                $categorie = new CatagorieLiens();                // On crée le formulaire grâce à l'ArticleType                $form = $this->createForm(new CategorieType(), $categorie);                // On redirige vers la page de visualisation de l'article nouvellement créé                return $this->redirect($this->generateUrl('carnaval_website_adm_categories'));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:adm_categorie_ajout.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTCATEGORIE"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admCategorieModifierAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:CatagorieLiens');        $categorie = $repository->find($id);        // On utiliser le ArticleEditType        $form = $this->createForm(new CategorieType(), $categorie);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_categories'));        if ($request->getMethod() == 'POST') {            $form->bind($request);            if ($form->isValid()) {                // On enregistre l'article                $em = $this->getDoctrine()->getManager();                $em->persist($categorie);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Categorie modifiée');                return $this->redirect($this->generateUrl('carnaval_website_adm_categories'));            }        }        return $this->render('CarnavalWebsiteBundle:Website:adm_categorie_modifier.html.twig', array(            'form'    => $form->createView(),            'video' => $categorie,            'formName' => "MODIFCATEGORIE"        ));    }    /* ===================================== EVENEMENTS ===================================== */    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admEvenementsAction(){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Evenement');        $listeEvents = $repository->getAllActiveEvents();        return $this->render('CarnavalWebsiteBundle:Website:adm_events.html.twig', array('events' => $listeEvents));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admEvenementAjoutAction(){        $event = new Evenement();        // On crée le formulaire grâce à l'ArticleType        $form = $this->createForm(new EvenementType(), $event);        // On récupère la requête        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_events'));        // On vérifie qu'elle est de type POST        if ($request->getMethod() == 'POST') {            // On fait le lien Requête <-> Formulaire            $form->bind($request);            // On vérifie que les valeurs entrées sont correctes            // (Nous verrons la validation des objets en détail dans le prochain chapitre)            if ($form->isValid()) {                $event->setIsDeleted(false);                // On enregistre notre objet $article dans la base de données                $em = $this->getDoctrine()->getManager();                $em->persist($event);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Evénement Enregistré');                $categorie = new CatagorieLiens();                // On crée le formulaire grâce à l'ArticleType                $form = $this->createForm(new EvenementType(), $event);                // On redirige vers la page de visualisation de l'article nouvellement créé                return $this->redirect($this->generateUrl('carnaval_website_adm_events'));            }        }        // À ce stade :        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau        return $this->render('CarnavalWebsiteBundle:Website:adm_evenement_ajout.html.twig', array(            'form' => $form->createView(),            'formName' => "AJOUTEVENT"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admEvenementModifierAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Evenement');        $event = $repository->find($id);        // On utiliser le ArticleEditType        $form = $this->createForm(new EvenementType(), $event);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_events'));        if ($request->getMethod() == 'POST') {            $form->bind($request);            if ($form->isValid()) {                // On enregistre l'article                $em = $this->getDoctrine()->getManager();                $em->persist($event);                $em->flush();                // On définit un message flash                $this->get('session')->getFlashBag()->add('info', 'Evenement modifié');                return $this->redirect($this->generateUrl('carnaval_website_adm_events'));            }        }        return $this->render('CarnavalWebsiteBundle:Website:adm_evenement_modifier.html.twig', array(            'form'    => $form->createView(),            'video' => $event,            'formName' => "MODIFEVENT"        ));    }    /**     * @Secure(roles="ROLE_SUPER_ADMIN, ROLE_ADMIN")     */    public function admEvenementSupprimerAction($id){        $repository = $this->getDoctrine()            ->getManager()            ->getRepository('CarnavalWebsiteBundle:Evenement');        $event = $repository->find($id);        $request = $this->getRequest();        if( $request->get('cancel') == 'Cancel' )            return $this->redirect($this->generateUrl('carnaval_website_adm_events'));        if ($request->getMethod() == 'POST') {            $event->setIsDeleted(true);            // On enregistre l'article            $em = $this->getDoctrine()->getManager();            $em->persist($event);            $em->flush();            // On définit un message flash            $this->get('session')->getFlashBag()->add('info', 'Evenement supprimé');            return $this->redirect($this->generateUrl('carnaval_website_adm_events'));        }        return $this->render('CarnavalWebsiteBundle:Website:adm_evenement_supprimer.html.twig', array(            'event' => $event,            'formName' => "SUPPEVENT"        ));    }}