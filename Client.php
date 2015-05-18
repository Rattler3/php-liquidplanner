<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Library to provide simple access to the Liquid Planner API
 *
 * The public methods are structured to mimic the API routes published
 * by Liquid Planner at https://app.liquidplanner.com/api/help/urls.
 * Method names follow the route sequence with the common leading
 * elements generalised away, so for example this API route:
 *     api/workspaces/:workspace_id/tasks/:id/track_time
 * is exposed as a public method called:
 *     tasksTrackTime()
 *
 * Convenience methods for "create" and "delete" are also provided
 * even though they are implied in the API routes and not explicitly
 * named, so for example to create or delete tasks you can simply call:
 *     tasksCreate()
 *     tasksDelete()
 *
 * @author     Jonathan Oxer <jon.oxer@ivt.com.au>
 * @author     James Rattue <jamesrattue@hotmail.co.uk>
 * @copyright  2011 Internet Vision Technologies <www.ivt.com.au>
 * @version    2011-08-08
 */
namespace LiquidPlanner;

/**
 * Class to provide simple access to the Liquid Planner API
 */
class Client
{
    /**
     * Account email address
     */
    private $email = '';
    /** 
     * Acount password
     */
    private $password = '';
    /**
     * Url of the service
     * @see https://app.liquidplanner.com/api/help/urls
     */
    private $serviceUrl = '';
    /**
     * Base API url
     */
    private $baseUrl;
    /**
     * Debug flag
     * @var boolean $debug
     */
    public $debug = false;

    /**
     * Constructor
     * 
     * @param int $workspaceID
     * @param string $email
     * @param string $password
     */
    public function __construct($workspaceID, $email, $password)
    {
        $this->email      = $email;
        $this->password   = $password;
        $this->baseUrl	  = "https://app.liquidplanner.com/api";
        $this->serviceUrl = $this->baseUrl . "/workspaces/".$workspaceID;
    }

    /**
     * Deletes a comment on a client from Liquid Planner
     *
     * Pass the ID of a comment in Liquid Planner into this method and it
     * will be deleted from the workspace. The raw response from the
     * web service is returned so you can examine the result.
     *
     * @param int $clientId  the ID of the client in Liquid Planner
     * @param int $commentId the ID of the comment in Liquid Planner
     *
     * @return string raw response from the API
     *
     * @access public
     */
    public function clientsCommentsDelete($clientId, $commentId)
    {
        $url = $this->serviceUrl.'/clients/'.$clientId.'/comments/'.$commentId;
        $response = $this->delete($url);

        return($response);
    }

    /**
     * Retrieves the specified task or a list of all tasks
     *
     * @param int $taskid ID of task.
     * @param array $params Parameters to send such as date and count limiters.
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function tasks($taskid=NULL, $params=array())
    {
        $url = $this->serviceUrl.'/tasks'.($taskid ? '/'.$taskid : '').($params ? '?'.http_build_query($params) : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves timesheets optionally filtered by parameters.
     *
     * @param array $params Parameters to send such as date and count limiters.
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function timesheets($params=array())
    {
        $url = $this->serviceUrl.'/timesheets/'.($params ? '?'.http_build_query($params) : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves timesheet entries optionally filtered by parameters.
     *
     * @param int $timesheetid
     * @param array $params Parameters to send such as date and count limiters. Documentation here: http://www.liquidplanner.com/api-guide/technical-reference/filtering-timesheet-entries.html
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function timesheetEntries($timesheetid=NULL, $params=array())
    {
        $url = $this->serviceUrl.($timesheetid ? '/timesheets/'.$timesheetid : '').'/timesheet_entries'.($params ? '?'.http_build_query($params) : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Reterives timesheet entries for a task
     * 
     * @param null $taskId
     * @param array $params
     * @return array 
     */
    public function taskTimesheetEntries($taskId=NULL, $params=array())
    {
        $url = $this->serviceUrl.'/tasks/'.$taskId.'/timesheet_entries'.($params ? '?'.http_build_query($params) : '');
        $response = $this->get($url);
        return($response);
    }

    /**
     * Creates a new task in Liquid Planner
     *
     * @param array $data values to apply to the newly created task
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function tasksCreate(array $data)
    {
        $encodedTask = json_encode(array('task' => $data));
        $url = $this->serviceUrl.'/tasks';
        $response = $this->post($url, $encodedTask);

        return($response);
    }

    /**
     * Deletes a task from Liquid Planner
     *
     * Pass the ID of a task in Liquid Planner into this method and it
     * will be deleted from the workspace. The raw response from the
     * web service is returned so you can examine the result.
     *
     * @param int $id the ID of the task in Liquid Planner
     *
     * @return string raw response from the API
     *
     * @access public
     */
    public function tasksDelete($id)
    {
        $url = $this->serviceUrl.'/tasks/'.$id;
        $response = $this->delete($url);

        return($response);
    }

    /**
     * Updates task time values, such as work completed and estimates
     *
     * @param array $data   values to apply to the specified task
     * @param int   $taskid ID of Liquid Planner task to update
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function tasksTrackTime(array $data, $taskid)
    {
        $encodedTask = json_encode($data);
        $url = $this->serviceUrl.'/tasks/'.$taskid.'/track_time';
        $response = $this->post($url, $encodedTask);

        return($response);
    }

    /**
     * Creates a new comment on a task in Liquid Planner
     *
     * @param array $data   values to apply to the newly created comment
     * @param int   $taskid ID of Liquid Planner task to link to comment
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function tasksCommentsCreate(array $data, $taskid)
    {
        $encodedData = json_encode(array('comment' => $data));
        $url = $this->serviceUrl.'/tasks/'.$taskid.'/comments';
        $response = $this->post($url, $encodedData);

        return($response);
    }

    /**
     * Creates a note on a task in Liquid Planner
     *
     * @param array $data
     * @param int  $taskid ID of Liquid Planer task to link to comment
     *
     * @return array Response from Liquid Planner
     */
    public function tasksNoteCreate(array $data, $taskid)
    {
        $encodedData = json_encode(array('note' => $data));
        $url = $this->serviceUrl.'/tasks/'.$taskid.'/note';
        $response = $this->post($url, $encodedData);

        return($response);
    }

    /**
     * Creates a link on a task in Liquid Planner
     *
     * @param array $data
     * @param int  $taskid ID of Liquid Planer task to link to comment
     *
     * @return array Response from Liquid Planner
     */
    public function tasksLinkCreate(array $data, $taskid)
    {
        $encodedData = json_encode(array('link' => $data));
        $url = $this->serviceUrl.'/tasks/'.$taskid.'/links';
        $response = $this->post($url, $encodedData);

        return($response);
    }

    /**
     * Retrieves the logged in user's account information.
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function account()
    {
        $url = $this->baseUrl.'/account';
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves the current workspace details.
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function workspace()
    {
        $url = $this->serviceUrl;
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves the specified client or a list of clients
     *
     * @param int $clientid ID of client.
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function clients($clientid=NULL)
    {
        $url = $this->serviceUrl.'/clients'.($clientid ? '/'.$clientid : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Creates a new client in Liquid Planner
     *
     * @param string $name         name of this client
     * @param string $description  plain-text description of the client
     * @param string $external_ref arbitrary string; use e.g. to store a reference ID from an external system
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function clientsCreate($name, $description = '', $external_ref = '')
    {
        $encodedClient = json_encode(array('client' => array(
            'name' => $name,
            'description' => $description,
            'external_reference' => $external_ref
        )));
        $url = $this->serviceUrl.'/clients';
        $response = $this->post($url, $encodedClient);

        return($response);
    }

    /**
     * Gets a list of comments on a client from Liquid Planner
     *
     * @param int $clientid  ID of Liquid Planner client to get comments from
     * @param int $commentid ID of Liquid Planner client comment to get
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function clientsComments($clientid=NULL, $commentid=NULL)
    {
        $url = $this->serviceUrl.'/clients/'.$clientid.'/comments'.($commentid ? '/'.$commentid : '');
        echo $url;

        return $this->get($url);
    }

    /**
     * Retrieves the specified member or a list of members
     *
     * @return array Response from Liquid Planner
     * @param int $memberid
     * @access public
     */
    public function members($memberid=NULL)
    {
        $url = $this->serviceUrl.'/members'.($memberid? '/'.$memberid : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves one member
     *
     * @param int $memberid
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function member($memberid)
    {
        return($this->members($memberid));
    }

    /**
     * Creates a new project in Liquid Planner
     *
     * @param string $name               name of this project
     * @param int    $client_id          client ID associated with project
     * @param int    $parent_id          parent ID associated with project
     * @param string $description        plain-text description of the project
     * @param bool   $is_done            whether the project is done or not
     * @param string $done_on            date the project was done on
     * @param string $external_reference
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function projectsCreate($name, $client_id, $parent_id, $description = '', $is_done = false, $done_on = '', $external_reference = '')
    {
        $encodedClient = json_encode(array('project' => array(
            'name' => $name,
            'client_id' => $client_id,
            'parent_id' => $parent_id,
            'description' => $description,
            'is_done' => $is_done,
            'done_on' => $done_on,
            'external_reference' => $external_reference
        )));
        $url = $this->serviceUrl.'/projects';
        $response = $this->post($url, $encodedClient);

        return($response);
    }

    /**
     * Retrieves the specified project or a list of projects
     *
     * @param int $projectid ID of project
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function projects($projectid=NULL)
    {
        $url = $this->serviceUrl.'/projects'.($projectid ? '/'.$projectid : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Retrieves the specified activity or a list of activities
     *
     * @param int $activityid ID of activity.
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function activities($activityid=NULL)
    {
        $url = $this->serviceUrl.'/activities'.($activityid ? '/'.$activityid : '');
        $response = $this->get($url);

        return($response);
    }

    /**
     * Creates a new activity in Liquid Planner
     * @todo
     * @param array $data Values to apply to the newly created activity
     *
     * @return array Response from Liquid Planner
     *
     * @access public
     */
    public function activitiesCreate(array $data)
    {
        return array("Not yet implemented - expected soon");
        $encodedActivity = json_encode(array('activity' => $data));
        $url = $this->serviceUrl.'/activities';
        $response = $this->post($url, $encodedActivity);

        return($response);
    }


/**************************************************************/


    /**
     * Send data to the Liquid Planner API as a POST method with a JSON-encoded payload
     * 
     * @param string $url
     * @param string $encodedTask
     * @return array
     */
    private function post($url, $encodedTask)
    {
        /* Set up the CURL object and execute it */
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HEADER, false);                                       // Suppress display of the response header
        curl_setopt($conn, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // Must submit as JSON
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);                                // Return result as a string
        curl_setopt($conn, CURLOPT_POST, true);                                          // Submit data as an HTTP POST
        curl_setopt($conn, CURLOPT_POSTFIELDS, $encodedTask);                            // Set the POST field values
        curl_setopt($conn, CURLOPT_ENCODING, "");                                        // Prevent GZIP compression of response from LP
        curl_setopt($conn, CURLOPT_USERPWD, $this->email.":".$this->password);           // Authenticate
        curl_setopt($conn, CURLOPT_URL, $url);                                           // Set the service URL
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);                               // Accept any SSL certificate
        $response = curl_exec($conn);
        curl_close($conn);

        /* The response is JSON, so decode it and return the result as an array */
        $results = json_decode($response, true);

        /* Check for Throttling from the API */
        if ((isset($results['type']) && $results['type'] == "Error") && (isset($results['error']) && $results['error'] == "Throttled")) {
            //We're being throttled. Wait the right amount of time and call it again.
            $this->throttleMessage($results);
            sleep($this->getWaitTime($results['message']));

            return $this->post($url, $encodedTask);
        }

        return $results;
    }


    /**
     * Send data to the Liquid Planner API as a PUT method with a JSON-encoded payload
     * @param string $url
     * @param array $task
     * @return array
     */
    private function put($url, $task)
    {
        /* Set up the CURL object and execute it */
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HEADER, false);                                       // Suppress display of the response header
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);                                // Return result as a string
        curl_setopt($conn, CURLOPT_POST, false);                                         // Submit data as an HTTP POST
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, 'PUT');                                // Submit data as an HTTP POST
        curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($task));                            // Set the POST field values
        curl_setopt($conn, CURLOPT_ENCODING, "");                                        // Prevent GZIP compression of response from LP
        curl_setopt($conn, CURLOPT_USERPWD, $this->email.":".$this->password);           // Authenticate
        curl_setopt($conn, CURLOPT_URL, $url);                                           // Set the service URL
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);                               // Accept any SSL certificate
        $response = curl_exec($conn);
        curl_close($conn);

        /* The response is JSON, so decode it and return the result as an array */
        $results = json_decode($response, true);

        /* Check for Throttling from the API */
        if ((isset($results['type']) && $results['type'] == "Error") && (isset($results['error']) && $results['error'] == "Throttled")) {
            //We're being throttled. Wait the right amount of time and call it again.
            $this->throttleMessage($results);
            sleep($this->getWaitTime($results['message']));

            return $this->put($url, $task);
        }

        return $results;
    }

    /**
     * Send data to the Liquid Planner API as a GET method
     * 
     * @param string $url
     * @return array
     */
    private function get($url)
    {
        /* Set up the CURL object and execute it */
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HEADER, false);                                       // Suppress display of the response header
        curl_setopt($conn, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // Must submit as JSON
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);                                // Return result as a string
        curl_setopt($conn, CURLOPT_POST, false);                                          // Submit data as an HTTP POST
        curl_setopt($conn, CURLOPT_ENCODING, "");                                        // Prevent GZIP compression of response from LP
        curl_setopt($conn, CURLOPT_USERPWD, $this->email.":".$this->password);           // Authenticate
        curl_setopt($conn, CURLOPT_URL, $url);                                           // Set the service URL
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);                               // Accept any SSL certificate
        $response = curl_exec($conn);
        curl_close($conn);

        /* The response is JSON, so decode it and return the result as an array */
        $results = json_decode($response, true);

        /* Check for Throttling from the API */
        if ((isset($results['type']) && $results['type'] == "Error") && (isset($results['error']) && $results['error'] == "Throttled")) {
            //We're being throttled. Wait the right amount of time and call it again.
            $this->throttleMessage($results);
            sleep($this->getWaitTime($results['message']));

            return $this->get($url);
        }

        return $results;
    }


    /**
     * Send data to the Liquid Planner API as a DELETE method
     * 
     * @param string $url
     * 
     * @return array
     */
    private function delete($url)
    {
        /* Set up the CURL object and execute it */
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HEADER, false);                                       // Suppress display of the response header
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);                                // Return result as a string
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, "DELETE");                             // Connect as an HTTP DELETE
        curl_setopt($conn, CURLOPT_ENCODING, "");                                        // Prevent GZIP compression of response from LP
        curl_setopt($conn, CURLOPT_USERPWD, $this->email.":".$this->password);           // Authenticate
        curl_setopt($conn, CURLOPT_URL, $url);                                           // Set the service URL
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);                               // Accept any SSL certificate
        $response = curl_exec($conn);
        curl_close($conn);

        $results = json_decode($response, true);

        /* Check for Throttling from the API */
        if ((isset($results['type']) && $results['type'] == "Error") && (isset($results['error']) && $results['error'] == "Throttled")) {
            //We're being throttled. Wait the right amount of time and call it again.
            $this->throttleMessage($results);
            sleep($this->getWaitTime($results['message']));

            return $this->delete($url);
        }

        return $results;
    }

    /**
     * Returns the throttle Message
     * @param array $results
     */
    private function throttleMessage($results)
    {
        if ($this->debug === true) {
            echo '<p class="throttled">API Throttling in effect. ' . $results['message'] . '</p>';

            /* Clear the output buffer if it's turned on. */
            if (ob_get_level() !== 0) {
                ob_flush();
                flush();
            }
        }
    }

    /**
     * Returns the wait time
     * 
     * @param string $message
     * @return int
     */
    private function getWaitTime($message)
    {
        $regexp = "/Try again in ([0-9]{1,}) seconds/";
        preg_match($regexp, $message, $matches);

        if(is_numeric($matches[1]))
            return $matches[1] + 2;
        else
            return 15;
    }
}
